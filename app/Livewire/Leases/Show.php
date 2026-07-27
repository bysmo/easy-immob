<?php

namespace App\Livewire\Leases;

use App\Domain\Lease\Actions\ActivateLeaseAction;
use App\Domain\Lease\Actions\TerminateLeaseAction;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Services\LeaseContractGenerator;
use Livewire\Component;

class Show extends Component
{
    public Lease $lease;
    public string $contractHtml = '';

    public function mount(int $leaseId, LeaseContractGenerator $generator): void
    {
        $this->lease = Lease::with(['property.owner', 'tenant', 'template', 'rentSchedules'])
            ->where('id', $leaseId)
            ->first() ?? abort(404);

        $this->authorize('view', $this->lease);

        if ($this->lease->template) {
            $this->contractHtml = $generator->generate($this->lease, $this->lease->template->content);
        }
    }

    public function activate(ActivateLeaseAction $action): void
    {
        $this->authorize('update', $this->lease);

        try {
            $this->lease = $action->execute($this->lease);
            session()->flash('success', "Le contrat a été activé avec succès. Les échéances de loyer ont été créées.");
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function terminate(TerminateLeaseAction $action): void
    {
        $this->authorize('update', $this->lease);

        $this->lease = $action->execute($this->lease);
        session()->flash('success', "Le contrat a été résilié.");
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.leases.show');
    }
}
