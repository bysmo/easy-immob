<?php

namespace App\Livewire\OwnerPortal;

use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Biens du bailleur, groupés par agence.
 */
class Properties extends Component
{
    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $agencyFilter = '';

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        if (! $owner) {
            abort(403);
        }

        $query = Property::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->with(['propertyType', 'managementContract.agency'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->agencyFilter, fn ($q) => $q->where('agency_id', $this->agencyFilter))
            ->orderBy('agency_id')
            ->orderBy('title');

        $properties = $query->get();

        // Grouper par agence
        $grouped = $properties->groupBy(fn ($p) => $p->managementContract?->agency?->name ?? 'Sans mandat');

        // Liste des agences disponibles pour le filtre
        $agencies = $properties
            ->map(fn ($p) => $p->managementContract?->agency)
            ->filter()
            ->unique('id')
            ->values();

        return view('livewire.owner-portal.properties', [
            'owner'    => $owner,
            'grouped'  => $grouped,
            'agencies' => $agencies,
        ]);
    }
}
