<?php

namespace App\Livewire\Tenants;

use App\Application\Services\DynamicMailConfigurator;
use App\Application\Services\ReferenceGenerator;
use App\Domain\Tenant\Models\Tenant;
use App\Mail\TenantInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    public function save(?ReferenceGenerator $generator = null): void
    {
        $generator = $generator ?? app(ReferenceGenerator::class);

        $this->authorize('create', Tenant::class);
        $this->validate();

        /** @var User $authUser */
        $authUser = Auth::user();

        // Contrôles anti-doublon au sein de l'agence
        if ($this->email) {
            $cleanEmail = mb_strtolower(trim($this->email));
            $existingTenantEmail = Tenant::where('agency_id', $authUser->agency_id)
                ->where('email', $cleanEmail)
                ->exists();

            if ($existingTenantEmail) {
                $this->addError('email', 'Un locataire avec cette adresse email existe déjà dans votre agence.');
                return;
            }
        }

        if ($this->id_card_number) {
            $cleanCnib = trim($this->id_card_number);
            $existingTenantCnib = Tenant::where('agency_id', $authUser->agency_id)
                ->where('id_card_number', $cleanCnib)
                ->exists();

            if ($existingTenantCnib) {
                $this->addError('id_card_number', "Un locataire avec ce numéro de pièce d'identité existe déjà dans votre agence.");
                return;
            }
        }

        $reference = $generator->generate(Tenant::class, $authUser->agency_id, 'LOC');

        $tenant = DB::transaction(function () use ($reference, $authUser) {
            $tenant = Tenant::create([
                'agency_id'         => $authUser->agency_id,
                'reference'         => $reference,
                'first_name'        => $this->first_name,
                'last_name'         => $this->last_name,
                'email'             => $this->email ? mb_strtolower(trim($this->email)) : null,
                'phone'             => $this->phone,
                'address'           => $this->address,
                'profession'        => $this->profession,
                'nationality'       => $this->nationality,
                'id_card_number'    => $this->id_card_number,
                'emergency_contact' => $this->emergency_contact,
                'status'            => $this->status,
            ]);

            if ($tenant->email) {
                $this->createOrLinkPortalUser($tenant, $authUser);
            }

            return $tenant;
        });

        session()->flash('success', "Le locataire {$tenant->full_name} a été créé avec la référence {$tenant->reference}.");

        $this->redirect(route('tenants.index'), navigate: false);
    }

    /**
     * Crée ou lie un compte utilisateur portail au locataire,
     * puis envoie le mail d'invitation.
     */
    private function createOrLinkPortalUser(Tenant $tenant, User $authUser): void
    {
        $user = User::withoutGlobalScopes()
            ->where('email', mb_strtolower($tenant->email))
            ->first();

        if (! $user) {
            $user = User::create([
                'agency_id' => $tenant->agency_id,
                'name'      => $tenant->full_name,
                'email'     => mb_strtolower($tenant->email),
                'password'  => Hash::make(Str::random(32)),
            ]);
        }

        $user->assignRole('Locataire');
        $tenant->update(['user_id' => $user->id]);

        $signedUrl = URL::temporarySignedRoute(
            'tenant-portal.activate',
            now()->addHours(72),
            ['user' => $user->id],
        );

        $agencyName = $authUser->agency?->name ?? 'EasyImmob';
        DynamicMailConfigurator::apply($authUser?->agency);

        try {
            Mail::to($user->email)->send(
                new TenantInvitationMail($user, $tenant, $signedUrl, $agencyName)
            );
        } catch (\Throwable $e) {
            Log::error("Erreur lors de l'envoi du mail d'invitation locataire : " . $e->getMessage());
            session()->flash('error', "Locataire créé mais impossible d'envoyer l'email d'invitation : " . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.tenants.create');
    }
}
