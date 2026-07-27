<?php

namespace App\Livewire\Arrears;

use App\Domain\Arrears\Actions\SendReminderAction;
use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Services\ArrearDetector;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $severityFilter = '';
    public string $statusFilter = '';

    public function mount(ArrearDetector $detector): void
    {
        // Détection automatique à l'affichage
        $detector->detect();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sendReminder(int $arrearId, SendReminderAction $action): void
    {
        $arrear = Arrear::where('id', $arrearId)->firstOrFail();
        $this->authorize('manage', $arrear);

        $action->execute($arrear, 'email');

        session()->flash('success', "Une relance e-mail a été envoyée avec succès à {$arrear->tenant?->full_name}.");
    }

    public function render(): \Illuminate\View\View
    {
        $arrears = Arrear::with(['tenant', 'lease.property', 'rentSchedule'])
            ->when($this->search, function ($q) {
                $q->whereHas('tenant', function ($tenantQ) {
                    $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('lease.property', function ($propQ) {
                    $propQ->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->severityFilter, fn ($q) => $q->where('severity', $this->severityFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('first_overdue_date')
            ->paginate(15);

        return view('livewire.arrears.index', [
            'arrears'         => $arrears,
            'severityOptions' => ArrearSeverity::options(),
            'statusOptions'   => ArrearStatus::options(),
        ]);
    }
}
