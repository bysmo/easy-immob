<?php

namespace App\Livewire\Reports;

use App\Domain\Owner\Models\Owner;
use App\Domain\Report\Services\OwnerStatementService;
use Livewire\Component;

class OwnerStatements extends Component
{
    public ?int $selectedOwnerId = null;
    public float $managementFeePercentage = 8.0;
    public ?string $period = null;

    public function mount(): void
    {
        $firstOwner = Owner::first();
        if ($firstOwner) {
            $this->selectedOwnerId = $firstOwner->id;
        }
    }

    public function render(OwnerStatementService $service): \Illuminate\View\View
    {
        $owners = Owner::orderBy('last_name')->get();
        $statement = null;

        if ($this->selectedOwnerId) {
            $owner = Owner::where('id', $this->selectedOwnerId)->first();
            if ($owner) {
                $statement = $service->generateStatement($owner, $this->managementFeePercentage, $this->period);
            }
        }

        return view('livewire.reports.owner-statements', compact('owners', 'statement'));
    }
}
