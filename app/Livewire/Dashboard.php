<?php

namespace App\Livewire;

use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\Owner;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Statistiques de l'agence courante.
     */
    public int $usersCount = 0;
    public int $ownersCount = 0;
    public int $tenantsCount = 0;
    public int $activeLeasesCount = 0;

    public function mount(): void
    {
        // Sans scope global qui filtre déjà par agency_id,
        // on restreint explicitement à l'agence de l'utilisateur connecté.
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->usersCount = User::withoutGlobalScopes()
            ->where('agency_id', $user->agency_id)
            ->count();

        $this->ownersCount = Owner::count();
        $this->tenantsCount = Tenant::count();
        $this->activeLeasesCount = Lease::where('status', 'active')->count();
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
