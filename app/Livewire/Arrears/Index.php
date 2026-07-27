<?php

namespace App\Livewire\Arrears;

use App\Domain\Arrears\Actions\SendReminderAction;
use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Services\ArrearDetector;
use App\Livewire\Traits\WithDataTable;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $severityFilter = '';
    public string $statusFilter = '';

    public function mount(ArrearDetector $detector): void
    {
        $detector->detect();
        $this->sortField = 'first_overdue_date';
        $this->sortDirection = 'desc';
    }

    public function updatedSeverityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
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
        $query = Arrear::with(['tenant', 'lease.property', 'rentSchedule'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('tenant', function ($tenantQ) {
                        $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    })->orWhereHas('lease.property', function ($propQ) {
                        $propQ->where('title', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->severityFilter, fn ($q) => $q->where('severity', $this->severityFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $arrears = $this->applySorting($query, 'first_overdue_date', 'desc')->paginate($this->perPage);

        return view('livewire.arrears.index', [
            'arrears'         => $arrears,
            'severityOptions' => ArrearSeverity::options(),
            'statusOptions'   => ArrearStatus::options(),
        ]);
    }
}
