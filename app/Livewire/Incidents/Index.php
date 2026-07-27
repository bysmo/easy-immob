<?php

namespace App\Livewire\Incidents;

use App\Domain\Incident\Models\Incident;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Incident::query()
            ->with(['property', 'tenant', 'lease']);

        if ($user->isTenant()) {
            $tenant = $user->tenant;
            $query->where('tenant_id', $tenant?->id ?? 0);
        } else {
            $query->where('agency_id', $user->agency_id);
        }

        $query->when($this->search, function ($q) {
            $q->where(function ($query) {
                $query->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('property', fn ($p) => $p->where('title', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('tenant', fn ($t) => $t->where('first_name', 'like', '%' . $this->search . '%')->orWhere('last_name', 'like', '%' . $this->search . '%'));
            });
        })
        ->when($this->statusFilter, function ($q) {
            $q->where('status', $this->statusFilter);
        });

        $incidents = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.incidents.index', compact('incidents'));
    }
}
