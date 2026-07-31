<?php

namespace App\Livewire\Owners;

use App\Domain\Owner\Models\Owner;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Owner $owner;

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $company_name = null;

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;

    #[Validate('nullable|string|max:255')]
    public ?string $phone = null;

    #[Validate('nullable|string')]
    public ?string $address = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function mount(int $ownerId): void
    {
        $this->owner = Owner::where('id', $ownerId)->first() ?? abort(404);
        $this->authorize('update', $this->owner);

        $this->first_name   = $this->owner->first_name;
        $this->last_name    = $this->owner->last_name;
        $this->company_name = $this->owner->company_name;
        $this->email        = $this->owner->email;
        $this->phone        = $this->owner->phone;
        $this->address      = $this->owner->address;
        $this->status       = $this->owner->status;
    }

    public function save(): void
    {
        $this->authorize('update', $this->owner);
        $this->validate();

        $this->owner->update([
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'company_name' => $this->company_name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'status'       => $this->status,
        ]);

        session()->flash('success', "Le bailleur {$this->owner->full_name} a été mis à jour.");

        $this->redirect(route('owners.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.owners.edit');
    }
}
