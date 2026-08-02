<?php

namespace App\Livewire\Owners;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Owner\Models\Owner;
use App\Mail\OwnerInvitationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $reference = $generator->generate(Owner::class, $authUser->agency_id, 'PRO');

        $owner = DB::transaction(function () use ($reference, $authUser) {
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

            // Si un email est renseigné, créer ou lier un compte portail bailleur
            if ($this->email) {
                $this->createOrLinkPortalUser($owner, $authUser);
            }

            return $owner;
        });

        session()->flash('success', "Le bailleur {$owner->full_name} a été créé avec la référence {$owner->reference}.");

        $this->redirect(route('owners.index'), navigate: false);
    }

    /**
     * Crée ou lie un compte utilisateur portail au bailleur,
     * puis envoie le mail d'invitation.
     */
    private function createOrLinkPortalUser(Owner $owner, \App\Models\User $authUser): void
    {
        // Chercher un utilisateur existant avec ce mail
        $user = \App\Models\User::withoutGlobalScopes()
            ->where('email', mb_strtolower($this->email))
            ->first();

        if (! $user) {
            $user = \App\Models\User::create([
                'agency_id' => $owner->agency_id,
                'name'      => $owner->full_name,
                'email'     => mb_strtolower($this->email),
                'password'  => Hash::make(Str::random(32)),
            ]);
        }

        // Assigner le rôle Bailleur (idempotent)
        $user->assignRole('Bailleur');

        // Lier le user au owner
        $owner->update(['user_id' => $user->id]);

        // Générer la Signed URL valable 72 h
        $signedUrl = URL::temporarySignedRoute(
            'owner-portal.activate',
            now()->addHours(72),
            ['user' => $user->id],
        );

        // Envoyer le mail d'invitation
        $agencyName = $authUser->agency?->name ?? 'EasyImmob';

        \App\Application\Services\DynamicMailConfigurator::apply($authUser?->agency);

        try {
            Mail::to($user->email)->send(
                new OwnerInvitationMail($user, $owner, $signedUrl, $agencyName)
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erreur lors de l'envoi du mail d'invitation bailleur : " . $e->getMessage());
            session()->flash('error', "Bailleur créé mais impossible d'envoyer l'email d'invitation : " . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.owners.create');
    }
}
