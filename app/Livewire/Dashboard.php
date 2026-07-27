<?php

namespace App\Livewire;

use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\Owner;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public int $usersCount = 0;
    public int $ownersCount = 0;
    public int $tenantsCount = 0;
    public int $activeLeasesCount = 0;

    // Tenant data
    public ?Tenant $tenant = null;
    public $tenantLeases;
    public $tenantRentSchedules;
    public $tenantIncidents;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isTenant()) {
            $this->tenant = Tenant::withoutGlobalScopes()->where('user_id', $user->id)->first();

            if ($this->tenant) {
                $this->tenantLeases = Lease::withoutGlobalScopes()->with(['property', 'agency'])
                    ->where('tenant_id', $this->tenant->id)
                    ->get();

                $leaseIds = $this->tenantLeases->pluck('id')->toArray();

                $this->tenantRentSchedules = RentSchedule::withoutGlobalScopes()->with(['lease.property'])
                    ->whereIn('lease_id', $leaseIds)
                    ->orderBy('due_date', 'desc')
                    ->get();

                $this->tenantIncidents = Incident::with(['property'])
                    ->where('tenant_id', $this->tenant->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }
        } else {
            $this->usersCount = User::withoutGlobalScopes()
                ->where('agency_id', $user->agency_id)
                ->count();

            $this->ownersCount = Owner::count();
            $this->tenantsCount = Tenant::count();
            $this->activeLeasesCount = Lease::where('status', 'active')->count();
        }
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('livewire.dashboard', [
            'agencyName' => $user->agency?->name ?? '—',
            'userName'   => $user->name,
            'roleName'   => $user->getRoleNames()->first() ?? '—',
        ]);
    }
}
