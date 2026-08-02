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
        $this->authorize('view', $this->tenant);

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
        $this->tenant->load('user');
        if ($this->tenant->hasPortalAccess()) {
            session()->flash('error', "Le portail de ce locataire est attribué ou actif. Ses informations ne peuvent plus être modifiées par l'agence.");
            return;
        }

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

    public function sendInvitation(): void
    {
        $this->authorize('update', $this->tenant);

        if (! $this->tenant->email) {
            session()->flash('error', "Ce locataire n'a pas d'adresse email.");
            return;
        }

        /** @var \App\Models\User $authUser */
        $authUser = \Illuminate\Support\Facades\Auth::user();

        $user = \App\Models\User::withoutGlobalScopes()
            ->where('email', mb_strtolower($this->tenant->email))
            ->first();

        if (! $user) {
            $user = \App\Models\User::create([
                'agency_id' => $this->tenant->agency_id,
                'name'      => $this->tenant->full_name,
                'email'     => mb_strtolower($this->tenant->email),
                'password'  => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            ]);
        }

        $user->assignRole('Locataire');
        $this->tenant->update(['user_id' => $user->id]);

        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'tenant-portal.activate',
            now()->addHours(72),
            ['user' => $user->id],
        );

        \App\Application\Services\DynamicMailConfigurator::apply($authUser?->agency);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\TenantInvitationMail($user, $this->tenant, $signedUrl, $authUser?->agency?->name ?? 'EasyImmob')
            );
            session()->flash('success', "L'invitation au portail locataire a été envoyée à {$this->tenant->email}.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erreur lors de l'envoi du mail d'invitation locataire : " . $e->getMessage());
            session()->flash('error', "Impossible d'envoyer l'email d'invitation : " . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.edit');
    }
}
