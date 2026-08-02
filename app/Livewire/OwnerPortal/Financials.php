<?php

namespace App\Livewire\OwnerPortal;

use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Models\OwnerPayout;
use App\Domain\Property\Models\Property;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Récap financier du bailleur par période.
 */
class Financials extends Component
{
    #[Url]
    public string $period = '';

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

        // Agences partenaires du bailleur
        $agencies = \App\Domain\Agency\Models\Agency::whereIn('id', function ($q) use ($owner) {
            $q->select('agency_id')
              ->from('properties')
              ->where('owner_id', $owner->id)
              ->whereNotNull('agency_id');
        })->orderBy('name')->get();

        $propertyIds = Property::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->when($this->agencyFilter, fn ($q) => $q->where('agency_id', $this->agencyFilter))
            ->pluck('id');

        // Périodes disponibles
        $availablePeriods = RentSchedule::withoutGlobalScopes()
            ->join('leases', 'rent_schedules.lease_id', '=', 'leases.id')
            ->whereIn('leases.property_id', $propertyIds)
            ->select('rent_schedules.period')
            ->distinct()
            ->orderBy('rent_schedules.period', 'desc')
            ->pluck('rent_schedules.period')
            ->toArray();

        // Loyers par propriété sur la période
        $scheduleQuery = RentSchedule::withoutGlobalScopes()
            ->join('leases', 'rent_schedules.lease_id', '=', 'leases.id')
            ->join('properties', 'leases.property_id', '=', 'properties.id')
            ->whereIn('leases.property_id', $propertyIds)
            ->select(
                'rent_schedules.*',
                'properties.title as property_title',
                'properties.rent_amount',
                'properties.is_subject_to_irf',
                'properties.id as property_id_raw'
            )
            ->with(['lease.property'])
            ->when($this->period, fn ($q) => $q->where('rent_schedules.period', $this->period));

        $schedules = $scheduleQuery->get();

        // Reversements (payouts)
        $payoutsQuery = OwnerPayout::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->with(['items.property', 'agency'])
            ->when($this->agencyFilter, fn ($q) => $q->where('agency_id', $this->agencyFilter))
            ->when($this->period, fn ($q) => $q->where('period', $this->period))
            ->latest();

        $payouts = $payoutsQuery->get();

        // Totaux agrégés
        $totals = [
            'gross'      => (float) $schedules->sum('expected_amount'),
            'collected'  => (float) $schedules->sum('paid_amount'),
            'net_payout' => (float) $payouts->sum('net_amount'),
            'paid_out'   => (float) $payouts->sum('paid_amount'),
            'pending'    => (float) $payouts->where('status', '!=', 'paid')->sum('net_amount')
                            - (float) $payouts->where('status', '!=', 'paid')->sum('paid_amount'),
        ];

        return view('livewire.owner-portal.financials', [
            'owner'            => $owner,
            'agencies'         => $agencies,
            'availablePeriods' => $availablePeriods,
            'schedules'        => $schedules,
            'payouts'          => $payouts,
            'totals'           => $totals,
        ]);
    }
}
