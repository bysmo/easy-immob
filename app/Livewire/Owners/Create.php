<?php

namespace App\Livewire\Owners;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Owner\Models\Owner;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
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

    #[Validate('nullable|string|max:255')]
    public ?string $profession = null;

    #[Validate('nullable|string|max:255')]
    public string $nationality = 'Burkinabè';

    #[Validate('nullable|string|max:255')]
    public ?string $id_card_number = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function save(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Owner::class);
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reference = $generator->generate(Owner::class, $user->agency_id, 'PRO');

        $owner = Owner::create([
            'reference'      => $reference,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'company_name'   => $this->company_name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'address'        => $this->address,
            'profession'     => $this->profession,
            'nationality'    => $this->nationality,
            'id_card_number' => $this->id_card_number,
            'status'         => $this->status,
        ]);

        session()->flash('success', "Le bailleur {$owner->full_name} a été créé avec la référence {$owner->reference}.");

        $this->redirect(route('owners.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.owners.create');
    }
}
