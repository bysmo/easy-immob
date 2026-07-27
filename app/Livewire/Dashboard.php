<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Nombre d'utilisateurs rattachés à l'agence courante.
     */
    public int $usersCount = 0;

    public function mount(): void
    {
        // Sans scope global qui filtre déjà par agency_id,
        // on restreint explicitement à l'agence de l'utilisateur connecté.
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->usersCount = User::withoutGlobalScopes()
            ->where('agency_id', $user->agency_id)
            ->count();
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
