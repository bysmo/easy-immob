<?php

namespace App\Livewire\Owners;

use App\Domain\Owner\Actions\SettleOwnerPayoutAction;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Owner\Services\OwnerPayoutCalculatorService;
use App\Domain\Payment\Enums\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PayoutsIndex extends Component
{
    use WithFileUploads, WithPagination;

    // Filtres généraux
    public string $searchOwner = '';
    public string $selectedPeriod = '';
    public string $selectedStatus = 'all';

    // Modale de calcul
    public bool $showCalculationModal = false;
    public string $calcPeriod = '';
    public string $calcType = 'collected'; // 'collected' ou 'expected'
    public ?int $calcOwnerId = null;

    // Modale de règlement
    public bool $showSettlementModal = false;
    public ?int $settlePayoutId = null;
    public string $settleDate = '';
    public float $settleAmount = 0.0;
    public string $settleMethod = 'mobile_money';
    public ?string $settleReference = null;
    public mixed $settleProof = null;
    public ?string $settleNotes = null;

    // Modale de détails des lignes
    public bool $showDetailsModal = false;
    public ?int $detailPayoutId = null;

    public function mount(): void
    {
        $this->selectedPeriod = date('Y-m');
        $this->calcPeriod = date('Y-m');
        $this->settleDate = date('Y-m-d');
    }

    public function openCalculationModal(?int $ownerId = null): void
    {
        $this->calcOwnerId = $ownerId;
        $this->calcPeriod = $this->selectedPeriod ?: date('Y-m');
        $this->calcType = 'collected';
        $this->showCalculationModal = true;
    }

    public function runCalculation(OwnerPayoutCalculatorService $calculator): void
    {
        $this->validate([
            'calcPeriod'  => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'calcType'    => ['required', 'in:collected,expected'],
            'calcOwnerId' => ['nullable', 'integer', 'exists:owners,id'],
        ], [
            'calcPeriod.required' => 'La période est obligatoire (ex: 2026-07).',
            'calcPeriod.regex'    => 'Le format de la période doit être YYYY-MM (ex: 2026-07).',
            'calcType.required'   => 'Veuillez choisir le mode de calcul.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user || ! $user->agency) {
            session()->flash('error', 'Aucune agence associée à votre compte.');
            return;
        }

        $payouts = $calculator->calculateAndGeneratePayouts(
            agency: $user->agency,
            period: $this->calcPeriod,
            calculationType: $this->calcType,
            ownerId: $this->calcOwnerId,
            creator: $user
        );

        $count = $payouts->count();
        $this->showCalculationModal = false;
        $this->selectedPeriod = $this->calcPeriod;

        if ($count > 0) {
            session()->flash('success', "{$count} facture(s) de reversement générée(s) avec succès pour la période {$this->calcPeriod}.");
        } else {
            session()->flash('warning', "Aucun loyer éligible à reverser trouvé pour la période {$this->calcPeriod} en mode " . ($this->calcType === 'collected' ? 'loyers encaissés' : 'loyers attendus') . ".");
        }
    }

    public function openSettlementModal(int $payoutId): void
    {
        $payout = OwnerPayout::where('agency_id', Auth::user()?->agency_id)
            ->findOrFail($payoutId);

        $this->settlePayoutId = $payout->id;
        $this->settleDate = date('Y-m-d');
        $this->settleAmount = $payout->remaining_amount;
        $this->settleMethod = 'mobile_money';
        $this->settleReference = '';
        $this->settleProof = null;
        $this->settleNotes = '';
        $this->showSettlementModal = true;
    }

    public function saveSettlement(SettleOwnerPayoutAction $action): void
    {
        $payout = OwnerPayout::where('agency_id', Auth::user()?->agency_id)
            ->findOrFail($this->settlePayoutId);

        $maxAmount = $payout->remaining_amount;

        $this->validate([
            'settleDate'      => ['required', 'date'],
            'settleAmount'    => ['required', 'numeric', 'min:1', 'max:' . $maxAmount],
            'settleMethod'    => ['required', 'string'],
            'settleProof'     => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'], // Max 5MB
            'settleReference' => ['nullable', 'string', 'max:100'],
            'settleNotes'     => ['nullable', 'string', 'max:1000'],
        ], [
            'settleDate.required'   => 'La date de règlement est obligatoire.',
            'settleAmount.required' => 'Le montant du règlement est obligatoire.',
            'settleAmount.min'      => 'Le montant doit être supérieur à 0.',
            'settleAmount.max'      => "Le montant ne peut pas dépasser le solde restant de " . number_format($maxAmount, 0, ',', ' ') . " FCFA.",
            'settleMethod.required' => 'Le moyen de paiement est obligatoire.',
            'settleProof.mimes'     => 'Le fichier doit être une image (JPG, PNG) ou un document PDF.',
            'settleProof.max'       => 'La taille du fichier ne doit pas dépasser 5 Mo.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $action->execute($payout, [
            'payment_date'          => $this->settleDate,
            'amount'                => $this->settleAmount,
            'payment_method'        => $this->settleMethod,
            'transaction_reference' => $this->settleReference,
            'proof_document'        => $this->settleProof,
            'notes'                 => $this->settleNotes,
        ], $user);

        $this->showSettlementModal = false;
        session()->flash('success', "Le règlement de " . number_format($this->settleAmount, 0, ',', ' ') . " FCFA a été enregistré avec succès.");
    }

    public function openDetailsModal(int $payoutId): void
    {
        $this->detailPayoutId = $payoutId;
        $this->showDetailsModal = true;
    }

    public function render(): \Illuminate\View\View
    {
        $agencyId = Auth::user()?->agency_id;

        $query = OwnerPayout::with(['owner', 'items.property', 'settlements'])
            ->where('agency_id', $agencyId);

        if (! empty($this->selectedPeriod)) {
            $query->where('period', $this->selectedPeriod);
        }

        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }

        if (! empty($this->searchOwner)) {
            $query->whereHas('owner', function ($q) {
                $q->where('first_name', 'like', "%{$this->searchOwner}%")
                  ->orWhere('last_name', 'like', "%{$this->searchOwner}%")
                  ->orWhere('company_name', 'like', "%{$this->searchOwner}%")
                  ->orWhere('reference', 'like', "%{$this->searchOwner}%");
            });
        }

        // Stats globales pour la sélection
        $statsBase = OwnerPayout::where('agency_id', $agencyId);
        if (! empty($this->selectedPeriod)) {
            $statsBase->where('period', $this->selectedPeriod);
        }

        $stats = [
            'total_gross'      => (float) (clone $statsBase)->sum('gross_amount'),
            'total_commission' => (float) (clone $statsBase)->sum('commission_amount'),
            'total_net'        => (float) (clone $statsBase)->sum('net_amount'),
            'total_paid'       => (float) (clone $statsBase)->sum('paid_amount'),
            'total_pending'    => max(0, (float) (clone $statsBase)->sum('net_amount') - (float) (clone $statsBase)->sum('paid_amount')),
        ];

        $payouts = $query->orderBy('created_at', 'desc')->paginate(15);
        $owners = Owner::where('agency_id', $agencyId)->orderBy('last_name')->get();
        $detailPayout = $this->detailPayoutId ? OwnerPayout::with(['owner', 'items.property', 'items.rentSchedule', 'settlements'])->find($this->detailPayoutId) : null;
        $settlePayout = $this->settlePayoutId ? OwnerPayout::with('owner')->find($this->settlePayoutId) : null;

        return view('livewire.owners.payouts-index', [
            'payouts'       => $payouts,
            'owners'        => $owners,
            'stats'         => $stats,
            'detailPayout'  => $detailPayout,
            'settlePayout'  => $settlePayout,
            'paymentMethods'=> PaymentMethod::cases(),
        ]);
    }
}
