<?php

namespace App\Livewire\Deposits;

use App\Domain\Deposit\Actions\ReceiveDepositAction;
use App\Domain\Deposit\Actions\RefundDepositAction;
use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use App\Domain\Lease\Models\Lease;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    // Modal Réception
    public bool $showReceiveModal = false;
    public ?int $selectedDepositId = null;

    #[Validate('required|numeric|min:1')]
    public float $receive_amount = 0;

    #[Validate('required|date')]
    public string $received_at = '';

    // Modal Restitution / Retenue
    public bool $showRefundModal = false;

    #[Validate('required|numeric|min:0')]
    public float $refunded_amount = 0;

    #[Validate('required|numeric|min:0')]
    public float $retained_amount = 0;

    #[Validate('nullable|string')]
    public ?string $retention_reason = null;

    #[Validate('required|date')]
    public string $refunded_at = '';

    public function mount(): void
    {
        $this->received_at = now()->format('Y-m-d');
        $this->refunded_at = now()->format('Y-m-d');

        // Créer les fiches de caution pour les baux qui n'en ont pas encore
        $leasesWithoutDeposit = Lease::whereDoesntHave('deposits')
            ->where('deposit_amount', '>', 0)
            ->get();

        foreach ($leasesWithoutDeposit as $lease) {
            Deposit::create([
                'agency_id'       => $lease->agency_id,
                'lease_id'        => $lease->id,
                'expected_amount' => $lease->deposit_amount,
                'received_amount' => 0,
                'status'          => DepositStatus::Pending,
            ]);
        }
    }

    public function openReceiveModal(int $depositId): void
    {
        $deposit = Deposit::where('id', $depositId)->firstOrFail();

        $this->selectedDepositId = $deposit->id;
        $this->receive_amount    = (float) $deposit->expected_amount;
        $this->received_at       = now()->format('Y-m-d');

        $this->showReceiveModal  = true;
    }

    public function processReceive(ReceiveDepositAction $action): void
    {
        $this->validate([
            'receive_amount' => 'required|numeric|min:1',
            'received_at'     => 'required|date',
        ]);

        $deposit = Deposit::where('id', $this->selectedDepositId)->firstOrFail();
        $this->authorize('manage', $deposit);

        $action->execute($deposit, $this->receive_amount, $this->received_at);

        session()->flash('success', "La caution de " . number_format($this->receive_amount, 0, ',', ' ') . " FCFA a été enregistrée comme reçue.");
        $this->showReceiveModal = false;
    }

    public function openRefundModal(int $depositId): void
    {
        $deposit = Deposit::where('id', $depositId)->firstOrFail();

        $this->selectedDepositId = $deposit->id;
        $this->refunded_amount   = (float) $deposit->received_amount;
        $this->retained_amount   = 0;
        $this->retention_reason  = null;
        $this->refunded_at       = now()->format('Y-m-d');

        $this->showRefundModal   = true;
    }

    public function processRefund(RefundDepositAction $action): void
    {
        $deposit = Deposit::where('id', $this->selectedDepositId)->firstOrFail();
        $this->authorize('manage', $deposit);

        if ($this->retained_amount > 0 && empty(trim((string) $this->retention_reason))) {
            $this->addError('retention_reason', 'Le motif de la retenue est obligatoire.');
            return;
        }

        try {
            $action->execute(
                deposit: $deposit,
                refundedAmount: $this->refunded_amount,
                retainedAmount: $this->retained_amount,
                retentionReason: $this->retention_reason,
                refundedAt: $this->refunded_at
            );

            session()->flash('success', "La caution pour la référence {$deposit->lease?->reference} a été clôturée.");
            $this->showRefundModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        $deposits = Deposit::with(['lease.property', 'lease.tenant'])
            ->when($this->search, function ($q) {
                $q->whereHas('lease.tenant', function ($tenantQ) {
                    $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('lease.property', function ($propQ) {
                    $propQ->where('title', 'like', '%' . $this->search . '%');
                })->orWhereHas('lease', function ($leaseQ) {
                    $leaseQ->where('reference', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.deposits.index', [
            'deposits'      => $deposits,
            'statusOptions' => DepositStatus::options(),
        ]);
    }
}
