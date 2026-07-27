<?php

namespace App\Livewire\Rents;

use App\Domain\Payment\Actions\RecordPaymentAction;
use App\Domain\Payment\Enums\PaymentMethod;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    // Modal de paiement
    public bool $showPaymentModal = false;
    public ?int $selectedScheduleId = null;

    #[Validate('required|numeric|min:1')]
    public float $amount = 0;

    #[Validate('required|date')]
    public string $payment_date = '';

    #[Validate('required')]
    public string $payment_method = 'cash';

    #[Validate('nullable|string')]
    public ?string $notes = null;

    public function mount(): void
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openPaymentModal(int $scheduleId): void
    {
        $schedule = RentSchedule::where('id', $scheduleId)->firstOrFail();

        $this->selectedScheduleId = $schedule->id;
        $this->amount             = (float) $schedule->remaining_amount;
        $this->payment_date       = now()->format('Y-m-d');
        $this->payment_method     = 'cash';
        $this->notes              = null;

        $this->showPaymentModal   = true;
    }

    public function recordPayment(RecordPaymentAction $action): void
    {
        $this->validate();

        $schedule = RentSchedule::where('id', $this->selectedScheduleId)->firstOrFail();
        $this->authorize('create', \App\Domain\Payment\Models\Payment::class);

        try {
            $payment = $action->execute(
                schedule: $schedule,
                amount: $this->amount,
                paymentDate: $this->payment_date,
                paymentMethod: $this->payment_method,
                notes: $this->notes
            );

            session()->flash('success', "Le paiement de " . number_format($payment->amount, 0, ',', ' ') . " FCFA a été enregistré (Réf: {$payment->reference}).");
            $this->showPaymentModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        $schedules = RentSchedule::with(['lease.property', 'lease.tenant'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('period', 'like', '%' . $this->search . '%')
                        ->orWhereHas('lease.tenant', function ($tenantQ) {
                            $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('lease.property', function ($propQ) {
                            $propQ->where('title', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest('due_date')
            ->paginate(15);

        return view('livewire.rents.index', [
            'schedules'      => $schedules,
            'statusOptions'  => RentScheduleStatus::options(),
            'paymentMethods' => PaymentMethod::options(),
        ]);
    }
}
