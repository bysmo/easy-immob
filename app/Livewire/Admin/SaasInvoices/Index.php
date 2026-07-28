<?php

namespace App\Livewire\Admin\SaasInvoices;

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

    // Modal de création de facture
    public bool $showCreateModal = false;
    public ?int $agency_id = null;
    public ?int $subscription_plan_id = null;
    public string $billing_cycle = 'monthly';
    public float $amount = 0;
    public string $payment_method = 'Mobile Money (Orange Money)';
    public string $status = 'paid';
    public string $notes = '';

    // Modal d'encaissement (Marquer comme payée)
    public bool $showMarkPaidModal = false;
    public ?int $selectedInvoiceId = null;
    public string $markPaidMethod = 'Mobile Money (MTN MoMo)';

    public function mount(): void
    {
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function openCreateModal(): void
    {
        $firstAgency = Agency::first();
        $firstPlan = SubscriptionPlan::first();

        $this->agency_id = $firstAgency?->id;
        $this->subscription_plan_id = $firstPlan?->id;
        $this->billing_cycle = 'monthly';
        $this->amount = (float) ($firstPlan?->price_monthly ?? 25000);
        $this->status = 'paid';
        $this->notes = 'Facture souscription SaaS agence';
        $this->showCreateModal = true;
    }

    public function updatedSubscriptionPlanId($planId): void
    {
        $plan = SubscriptionPlan::find($planId);
        if ($plan) {
            $this->amount = $plan->getPriceForCycle($this->billing_cycle);
        }
    }

    public function updatedBillingCycle($cycle): void
    {
        $plan = SubscriptionPlan::find($this->subscription_plan_id);
        if ($plan) {
            $this->amount = $plan->getPriceForCycle($cycle);
        }
    }

    public function createInvoice(): void
    {
        $this->validate([
            'agency_id'            => 'required|exists:agencies,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle'        => 'required|in:monthly,yearly',
            'amount'               => 'required|numeric|min:0',
            'status'               => 'required|in:paid,unpaid,overdue',
        ]);

        SaasInvoice::create([
            'number'               => SaasInvoice::generateNumber(),
            'agency_id'            => $this->agency_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'billing_cycle'        => $this->billing_cycle,
            'amount'               => $this->amount,
            'tax_amount'           => 0,
            'total_amount'         => $this->amount,
            'status'               => $this->status,
            'invoice_date'         => now(),
            'due_date'             => now()->addDays(7),
            'paid_at'              => $this->status === 'paid' ? now() : null,
            'payment_method'       => $this->status === 'paid' ? $this->payment_method : null,
            'notes'                => $this->notes,
        ]);

        $this->showCreateModal = false;
        session()->flash('message', 'Nouvelle facture SaaS générée avec succès.');
    }

    public function openMarkPaidModal(int $invoiceId): void
    {
        $this->selectedInvoiceId = $invoiceId;
        $this->markPaidMethod = 'Mobile Money (Wave)';
        $this->showMarkPaidModal = true;
    }

    public function markAsPaid(): void
    {
        if (!$this->selectedInvoiceId) return;

        $invoice = SaasInvoice::findOrFail($this->selectedInvoiceId);
        $invoice->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => $this->markPaidMethod,
        ]);

        $this->showMarkPaidModal = false;
        session()->flash('message', "La facture {$invoice->number} a été marquée comme payée.");
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement au Super Admin SaaS.');
        }

        $agencies = Agency::all();
        $plans = SubscriptionPlan::all();

        $query = SaasInvoice::with(['agency', 'subscriptionPlan'])
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->where('number', 'like', '%'.$this->search.'%')
                   ->orWhereHas('agency', fn ($aq) => $aq->where('name', 'like', '%'.$this->search.'%'));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $invoices = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.saas-invoices.index', compact('invoices', 'agencies', 'plans'));
    }
}
