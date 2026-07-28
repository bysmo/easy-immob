<?php

namespace App\Livewire\Subscription;

use App\Domain\Agency\Models\Agency;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $selectedCycle = 'monthly';

    // Modal Confirmation & Erreurs
    public bool $showConfirmModal = false;
    public bool $showErrorModal = false;
    public string $errorMessage = '';
    public ?int $targetPlanId = null;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->agency) {
            $this->selectedCycle = $user->agency->billing_cycle ?? 'monthly';
        }
    }

    /**
     * Ouvre la modale de confirmation ou affiche la modale d'erreur si la rétrogradation est invalide
     */
    public function requestPlanChange(int $planId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $agency = Agency::with(['properties'])->findOrFail($user->agency_id);
        $plan = SubscriptionPlan::findOrFail($planId);

        // 1. Interdiction de passer ou rétrograder vers le plan Essai Gratuit
        if ($plan->slug === 'essai-gratuit' || ($plan->price_monthly == 0 && $plan->price_yearly == 0)) {
            $this->errorMessage = "L'offre Essai Gratuit (3 mois) est exclusivement attribuée lors de la création initiale d'une agence. Il n'est pas possible de souscrire ou de rétrograder vers la formule d'essai gratuit.";
            $this->showErrorModal = true;
            return;
        }

        // 2. Vérification de la capacité / quota de biens lors d'un changement de forfait
        $currentPropertiesCount = $agency->properties()->count();

        if (!$plan->isUnlimitedProperties() && $currentPropertiesCount > $plan->max_properties) {
            $this->errorMessage = "Impossible de passer au forfait '{$plan->name}'. Votre agence gérait actuellement {$currentPropertiesCount} bien(s), ce qui dépasse le quota maximal de {$plan->max_properties} bien(s) autorisé par cette formule. Veuillez d'abord supprimer ou archiver des biens avant de rétrograder.";
            $this->showErrorModal = true;
            return;
        }

        // Si tout est conforme, on ouvre la modale de confirmation personnalisée
        $this->targetPlanId = $planId;
        $this->showConfirmModal = true;
    }

    /**
     * Exécute le changement de forfait après confirmation de l'utilisateur dans la modale
     */
    public function executePlanChange(): void
    {
        if (!$this->targetPlanId) {
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $agency = Agency::findOrFail($user->agency_id);
        $plan = SubscriptionPlan::findOrFail($this->targetPlanId);

        // Mise à jour de l'abonnement de l'agence
        $agency->update([
            'subscription_plan_id' => $plan->id,
            'billing_cycle'        => $this->selectedCycle,
            'subscription_status'  => 'active',
            'subscription_ends_at' => now()->addMonths($this->selectedCycle === 'yearly' ? 12 : 1),
        ]);

        // Génération automatique d'une facture SaaS
        $amount = $plan->getPriceForCycle($this->selectedCycle);

        SaasInvoice::create([
            'number'               => SaasInvoice::generateNumber(),
            'agency_id'            => $agency->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle'        => $this->selectedCycle,
            'amount'               => $amount,
            'tax_amount'           => 0,
            'total_amount'         => $amount,
            'status'               => 'paid',
            'invoice_date'         => now(),
            'due_date'             => now()->addDays(7),
            'paid_at'              => now(),
            'payment_method'       => 'Paiement en ligne (Mobile Money / Carte)',
            'notes'                => "Souscription/Changement de formule vers l'offre {$plan->name} ({$this->selectedCycle}).",
        ]);

        $this->showConfirmModal = false;
        $this->targetPlanId = null;

        session()->flash('message', "Félicitations ! Votre abonnement a été mis à jour avec succès vers la formule '{$plan->name}' !");
    }

    public function closeModals(): void
    {
        $this->showConfirmModal = false;
        $this->showErrorModal = false;
        $this->errorMessage = '';
        $this->targetPlanId = null;
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $agency = $user?->agency ? Agency::with(['subscriptionPlan', 'properties'])->find($user->agency_id) : null;
        $plans = SubscriptionPlan::active()->get();
        $targetPlan = $this->targetPlanId ? SubscriptionPlan::find($this->targetPlanId) : null;

        $invoices = $agency
            ? SaasInvoice::where('agency_id', $agency->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('livewire.subscription.index', compact('agency', 'plans', 'invoices', 'targetPlan'));
    }
}
