<?php

namespace App\Livewire\Reports;

use App\Domain\Report\Services\FinancialReportService;
use Livewire\Component;

class Index extends Component
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
    }

    public function render(FinancialReportService $service): \Illuminate\View\View
    {
        $summary = $service->getSummary($this->startDate, $this->endDate);

        return view('livewire.reports.index', compact('summary'));
    }
}
