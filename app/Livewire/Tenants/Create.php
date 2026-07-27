<?php

namespace App\Livewire\Tenants;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;

    #[Validate('nullable|string|max:255')]
    public ?string $phone = null;

    #[Validate('nullable|string')]
    public ?string $address = null;

    #[Validate('nullable|string|max:255')]
    public ?string $emergency_contact = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function save(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Tenant::class);
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reference = $generator->generate(Tenant::class, $user->agency_id, 'LOC');

        $tenant = Tenant::create([
            'reference'         => $reference,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'address'           => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'status'            => $this->status,
        ]);

        session()->flash('success', "Le locataire {$tenant->full_name} a été créé avec la référence {$tenant->reference}.");

        $this->redirect(route('tenants.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.create');
    }
}
