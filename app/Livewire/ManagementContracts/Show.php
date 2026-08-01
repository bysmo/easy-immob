<?php

namespace App\Livewire\ManagementContracts;

use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Services\ManagementContractGenerator;
use Livewire\Component;

class Show extends Component
{
    public int $contractId;

    public function mount(int $contractId): void
    {
        $this->contractId = $contractId;
    }

    public function render(ManagementContractGenerator $generator)
    {
        $contract = ManagementContract::with(['owner', 'properties.propertyType', 'agency'])
            ->findOrFail($this->contractId);

        $generatedText = $generator->generateText($contract);

        return view('livewire.management-contracts.show', [
            'contract'      => $contract,
            'generatedText' => $generatedText,
        ])->layout('components.layouts.app');
    }
}
