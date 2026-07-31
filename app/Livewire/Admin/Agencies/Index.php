<?php

namespace App\Livewire\Admin\Agencies;

use App\Domain\Agency\Models\Agency;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';
    public string $planFilter = '';

    // Modal de modification du forfait
    public bool $showEditModal = false;
    public ?int $selectedAgencyId = null;
    public ?int $newPlanId = null;
    public string $newBillingCycle = 'monthly';
    public string $newStatus = 'active';
    public float $newCommissionRate = 10.0;
    public bool $newIsSubjectToTva = true;

    public function mount(): void
    {
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function openEditModal(int $agencyId): void
    {
        $agency = Agency::findOrFail($agencyId);
        $this->selectedAgencyId = $agency->id;
        $this->newPlanId = $agency->subscription_plan_id;
        $this->newBillingCycle = $agency->billing_cycle ?? 'monthly';
        $this->newStatus = $agency->status ?? 'active';
        $this->newCommissionRate = (float) ($agency->commission_rate ?? 10.0);
        $this->newIsSubjectToTva = (bool) ($agency->is_subject_to_tva ?? true);
        $this->showEditModal = true;
    }

    public function updateAgencySubscription(): void
    {
        $this->validate([
            'newPlanId'         => 'required|exists:subscription_plans,id',
            'newBillingCycle'   => 'required|in:monthly,yearly',
            'newStatus'         => 'required|in:active,suspended',
            'newCommissionRate' => 'required|numeric|min:0|max:100',
            'newIsSubjectToTva' => 'required|boolean',
        ]);

        if (!$this->selectedAgencyId) {
            return;
        }

        $agency = Agency::findOrFail($this->selectedAgencyId);
        $planChanged = $agency->subscription_plan_id != $this->newPlanId || $agency->billing_cycle != $this->newBillingCycle;

        $agency->update([
            'subscription_plan_id' => $this->newPlanId,
            'billing_cycle'        => $this->newBillingCycle,
            'status'               => $this->newStatus,
            'commission_rate'      => $this->newCommissionRate,
            'is_subject_to_tva'    => $this->newIsSubjectToTva,
        ]);

        // Si le forfait a été changé, on génère une nouvelle facture SaaS pour l'agence
        if ($planChanged) {
            $plan = SubscriptionPlan::find($this->newPlanId);
            if ($plan) {
                SaasInvoice::create([
                    'number'               => SaasInvoice::generateNumber(),
                    'agency_id'            => $agency->id,
                    'subscription_plan_id' => $plan->id,
                    'billing_cycle'        => $this->newBillingCycle,
                    'amount'               => $plan->getPriceForCycle($this->newBillingCycle),
                    'tax_amount'           => 0,
                    'total_amount'         => $plan->getPriceForCycle($this->newBillingCycle),
                    'status'               => 'paid',
                    'invoice_date'         => now(),
                    'due_date'             => now()->addDays(7),
                    'paid_at'              => now(),
                    'payment_method'       => 'Administration SaaS',
                    'notes'                => 'Facture générée automatiquement lors du changement d\'abonnement par l\'administrateur SaaS.',
                ]);
            }
        }

        $this->showEditModal = false;
        session()->flash('message', "L'abonnement de l'agence {$agency->name} a été mis à jour avec succès.");
    }

    public function toggleAgencyStatus(int $agencyId): void
    {
        $agency = Agency::findOrFail($agencyId);
        $agency->status = $agency->status === 'active' ? 'suspended' : 'active';
        $agency->save();

        session()->flash('message', "Le statut de l'agence {$agency->name} est maintenant : {$agency->status}.");
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement au Super Admin SaaS.');
        }

        $plans = SubscriptionPlan::active()->get();

        $query = Agency::with(['subscriptionPlan', 'properties'])
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->where('name', 'like', '%'.$this->search.'%')
                   ->orWhere('email', 'like', '%'.$this->search.'%')
                   ->orWhere('phone', 'like', '%'.$this->search.'%');
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->planFilter, fn ($q) => $q->where('subscription_plan_id', $this->planFilter));

        $agencies = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.agencies.index', compact('agencies', 'plans'));
    }
}
