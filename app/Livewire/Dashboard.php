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

    // Filtre période financière agence
    public string $financialPeriod = 'all';

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

                $activeLease = $this->tenantLeases->firstWhere('status.value', 'active') ?? $this->tenantLeases->first();

                if ($activeLease) {
                    $today = now()->startOfDay();

                    // Impayés & en retard pour le contrat actif
                    $unpaidSchedules = RentSchedule::withoutGlobalScopes()
                        ->with(['lease.property'])
                        ->where('lease_id', $activeLease->id)
                        ->where(function ($q) use ($today) {
                            $q->whereIn('status', [RentScheduleStatus::Overdue, RentScheduleStatus::PartiallyPaid])
                              ->orWhere(function ($q2) use ($today) {
                                  $q2->where('status', RentScheduleStatus::Pending)
                                     ->where('due_date', '<', $today);
                              });
                        })
                        ->get();

                    // 3 prochains à venir pour le contrat actif
                    $upcomingSchedules = RentSchedule::withoutGlobalScopes()
                        ->with(['lease.property'])
                        ->where('lease_id', $activeLease->id)
                        ->where('status', RentScheduleStatus::Pending)
                        ->where('due_date', '>=', $today)
                        ->orderBy('due_date', 'asc')
                        ->take(3)
                        ->get();

                    // Fusionner, dédoublonner et trier du plus vieux au plus récent (due_date ASC)
                    $this->tenantRentSchedules = $unpaidSchedules
                        ->merge($upcomingSchedules)
                        ->unique('id')
                        ->sortBy('due_date')
                        ->values();
                } else {
                    $this->tenantRentSchedules = collect();
                }

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

        $financialData = [];

        if (!$user->isTenant()) {
            $agency = $user->agency;
            $commissionRate = (float) ($agency?->commission_rate ?? 10.0);
            $isSubjectToTva = (bool) ($agency?->is_subject_to_tva ?? true);

            $availablePeriods = RentSchedule::select('period')
                ->distinct()
                ->orderBy('period', 'desc')
                ->pluck('period')
                ->toArray();

            $schedulesQuery = RentSchedule::query();

            if ($this->financialPeriod === 'current_month') {
                $schedulesQuery->where('period', now()->format('Y-m'));
            } elseif ($this->financialPeriod !== 'all' && !empty($this->financialPeriod)) {
                $schedulesQuery->where('period', $this->financialPeriod);
            }

            $schedules = $schedulesQuery->get();

            $totalExpectedRent = (float) $schedules->sum('expected_amount');
            $totalPaidRent     = (float) $schedules->sum('paid_amount');

            // Impayés : somme de remaining_amount pour les échéances non annulées et non totalement payées
            $totalUnpaidRent   = (float) $schedules
                ->whereIn('status.value', ['pending', 'partially_paid', 'overdue'])
                ->sum('remaining_amount');

            // Commission agence (% sur loyers encaissés)
            $totalCommission   = round(($totalPaidRent * $commissionRate) / 100, 2);

            // TVA 18% sur commission perçue si assujetti
            $totalTva          = $isSubjectToTva ? round(($totalCommission * 18) / 100, 2) : 0.0;

            $financialData = [
                'commissionRate'    => $commissionRate,
                'isSubjectToTva'    => $isSubjectToTva,
                'availablePeriods'  => $availablePeriods,
                'totalExpectedRent' => $totalExpectedRent,
                'totalPaidRent'     => $totalPaidRent,
                'totalUnpaidRent'   => $totalUnpaidRent,
                'totalCommission'   => $totalCommission,
                'totalTva'          => $totalTva,
            ];
        }

        return view('livewire.dashboard', array_merge([
            'agencyName' => $user->agency?->name ?? '—',
            'userName'   => $user->name,
            'roleName'   => $user->getRoleNames()->first() ?? '—',
        ], $financialData));
    }
}
