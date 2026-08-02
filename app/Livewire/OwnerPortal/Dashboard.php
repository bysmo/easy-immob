<?php

namespace App\Livewire\OwnerPortal;

use App\Domain\Incident\Models\Incident;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Property\Models\Property;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $owner = $user->owner;

        if (! $owner) {
            abort(403, 'Aucun bailleur associé à ce compte.');
        }

        $properties = Property::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->get();

        $propertyIds = $properties->pluck('id');

        $stats = [
            'total_properties'       => $properties->count(),
            'properties_rented'      => $properties->where('status.value', 'rented')->count(),
            'properties_available'   => $properties->where('status.value', 'available')->count(),
            'properties_maintenance' => $properties->where('status.value', 'maintenance')->count(),
            'pending_incidents'      => Incident::withoutGlobalScopes()
                ->whereIn('property_id', $propertyIds)
                ->whereIn('status', ['reported', 'in_progress'])
                ->count(),
            'contracts_active'       => ManagementContract::withoutGlobalScopes()
                ->where('owner_id', $owner->id)
                ->where('status', 'active')
                ->count(),
        ];

        // Reversements en attente
        $pendingPayouts = OwnerPayout::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->whereIn('status', ['pending', 'partial'])
            ->with(['owner'])
            ->latest()
            ->take(5)
            ->get();

        // Derniers incidents sur ses biens
        $recentIncidents = Incident::withoutGlobalScopes()
            ->whereIn('property_id', $propertyIds)
            ->with(['property'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.owner-portal.dashboard', [
            'owner'           => $owner,
            'stats'           => $stats,
            'pendingPayouts'  => $pendingPayouts,
            'recentIncidents' => $recentIncidents,
        ]);
    }
}
