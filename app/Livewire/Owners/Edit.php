<?php

namespace App\Livewire\Owners;

use App\Domain\Owner\Actions\SettleOwnerPayoutAction;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Payment\Enums\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Owner $owner;

    public string $activeTab = 'info'; // 'info', 'properties', 'payouts'

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $company_name = null;

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;

    #[Validate('nullable|string|max:255')]
    public ?string $phone = null;

    #[Validate('nullable|string')]
    public ?string $address = null;

    #[Validate('nullable|string|max:255')]
    public ?string $profession = null;

    #[Validate('nullable|string|max:255')]
    public string $nationality = 'Burkinabè';

    #[Validate('nullable|string|max:255')]
    public ?string $id_card_number = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    // Modale de règlement rapide sur la fiche bailleur
    public bool $showSettlementModal = false;
    public ?int $settlePayoutId = null;
    public string $settleDate = '';
    public float $settleAmount = 0.0;
    public string $settleMethod = 'mobile_money';
    public ?string $settleReference = null;
    public mixed $settleProof = null;
    public ?string $settleNotes = null;

    public function mount(int $ownerId): void
    {
        $this->owner = Owner::where('id', $ownerId)->first() ?? abort(404);
        $this->authorize('update', $this->owner);

        $this->first_name     = $this->owner->first_name;
        $this->last_name      = $this->owner->last_name;
        $this->company_name   = $this->owner->company_name;
        $this->email          = $this->owner->email;
        $this->phone          = $this->owner->phone;
        $this->address        = $this->owner->address;
        $this->profession     = $this->owner->profession;
        $this->nationality    = $this->owner->nationality ?? 'Burkinabè';
        $this->id_card_number = $this->owner->id_card_number;
        $this->status         = $this->owner->status;
        $this->settleDate     = date('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function save(): void
    {
        $this->authorize('update', $this->owner);
        $this->validate();

        $this->owner->update([
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'company_name'   => $this->company_name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'address'        => $this->address,
            'profession'     => $this->profession,
            'nationality'    => $this->nationality,
            'id_card_number' => $this->id_card_number,
            'status'         => $this->status,
        ]);

        session()->flash('success', "Le bailleur {$this->owner->full_name} a été mis à jour.");

        $this->redirect(route('owners.index'), navigate: false);
    }

    public function openSettlementModal(int $payoutId): void
    {
        $payout = OwnerPayout::where('agency_id', Auth::user()?->agency_id)
            ->where('owner_id', $this->owner->id)
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
            ->where('owner_id', $this->owner->id)
            ->findOrFail($this->settlePayoutId);

        $maxAmount = $payout->remaining_amount;

        $this->validate([
            'settleDate'      => ['required', 'date'],
            'settleAmount'    => ['required', 'numeric', 'min:1', 'max:' . $maxAmount],
            'settleMethod'    => ['required', 'string'],
            'settleProof'     => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'settleReference' => ['nullable', 'string', 'max:100'],
            'settleNotes'     => ['nullable', 'string', 'max:1000'],
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

    public function render(): \Illuminate\View\View
    {
        $properties   = $this->owner->properties()->with(['propertyType', 'managementContract'])->get();
        $contracts    = $this->owner->managementContracts()->with(['properties'])->get();
        $payouts      = $this->owner->payouts()->with(['items.property', 'settlements'])->get();
        $settlements  = $this->owner->payoutSettlements()->with(['ownerPayout'])->get();
        $settlePayout = $this->settlePayoutId ? OwnerPayout::find($this->settlePayoutId) : null;

        $totalNet     = (float) $payouts->sum('net_amount');
        $totalPaid    = (float) $payouts->sum('paid_amount');
        $totalPending = max(0, $totalNet - $totalPaid);

        return view('livewire.owners.edit', [
            'properties'    => $properties,
            'contracts'     => $contracts,
            'payouts'       => $payouts,
            'settlements'   => $settlements,
            'settlePayout'  => $settlePayout,
            'totalNet'      => $totalNet,
            'totalPaid'     => $totalPaid,
            'totalPending'  => $totalPending,
            'paymentMethods'=> PaymentMethod::cases(),
        ]);
    }
}
