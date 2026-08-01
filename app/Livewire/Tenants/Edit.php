<?php

namespace App\Livewire\Tenants;

use App\Domain\Tenant\Models\Tenant;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Tenant $tenant;

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
    public ?string $profession = null;

    #[Validate('nullable|string|max:255')]
    public string $nationality = 'Burkinabè';

    #[Validate('nullable|string|max:255')]
    public ?string $id_card_number = null;

    #[Validate('nullable|string|max:255')]
    public ?string $emergency_contact = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(int $tenantId): void
    {
        $this->tenant = Tenant::where('id', $tenantId)->first() ?? abort(404);
        $this->authorize('update', $this->tenant);

        $this->first_name        = $this->tenant->first_name;
        $this->last_name         = $this->tenant->last_name;
        $this->email             = $this->tenant->email;
        $this->phone             = $this->tenant->phone;
        $this->address           = $this->tenant->address;
        $this->profession        = $this->tenant->profession;
        $this->nationality       = $this->tenant->nationality ?? 'Burkinabè';
        $this->id_card_number    = $this->tenant->id_card_number;
        $this->emergency_contact = $this->tenant->emergency_contact;
        $this->status            = $this->tenant->status;
    }

    public function save(): void
    {
        $this->authorize('update', $this->tenant);
        $this->validate();

        $this->tenant->update([
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'address'           => $this->address,
            'profession'        => $this->profession,
            'nationality'       => $this->nationality,
            'id_card_number'    => $this->id_card_number,
            'emergency_contact' => $this->emergency_contact,
            'status'            => $this->status,
        ]);

        session()->flash('success', "Le locataire {$this->tenant->full_name} a été mis à jour.");

        $this->redirect(route('tenants.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.edit');
    }
}
