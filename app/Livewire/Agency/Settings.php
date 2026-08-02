<?php

namespace App\Livewire\Agency;

use App\Application\Services\DynamicMailConfigurator;
use App\Domain\Agency\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public string $name = '';
    public ?string $legal_name = null;
    public ?string $manager_name = null;
    public ?string $manager_title = null;
    public ?string $manager_phone = null;
    public ?string $manager_id_card = null;
    public string $email = '';
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $nif_rccm = null;

    public float $commission_rate = 10.0;
    public bool $is_subject_to_tva = true;
    public float $tva_rate = 18.0;

    // ----- Configuration Serveur Mail (SMTP) -----
    public string $mail_mailer = 'smtp';
    public ?string $mail_host = null;
    public ?int $mail_port = 587;
    public ?string $mail_username = null;
    public ?string $mail_password = null;
    public ?string $mail_encryption = 'tls';
    public ?string $mail_from_address = null;
    public ?string $mail_from_name = null;

    /** @var mixed */
    public $logo = null;
    public ?string $existingLogoUrl = null;

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            abort(403, 'Accès réservé aux utilisateurs rattachés à une agence.');
        }

        $agency = Agency::findOrFail($user->agency_id);

        $this->name              = $agency->name ?? '';
        $this->legal_name         = $agency->legal_name;
        $this->manager_name      = $agency->manager_name;
        $this->manager_title     = $agency->manager_title;
        $this->manager_phone     = $agency->manager_phone;
        $this->manager_id_card   = $agency->manager_id_card;
        $this->email             = $agency->email ?? '';
        $this->phone             = $agency->phone;
        $this->address           = $agency->address;
        $this->nif_rccm          = $agency->nif_rccm;
        $this->commission_rate   = (float) ($agency->commission_rate ?? 10.0);
        $this->is_subject_to_tva = (bool) ($agency->is_subject_to_tva ?? true);
        $this->tva_rate          = (float) ($agency->tva_rate ?? 18.0);
        $this->existingLogoUrl   = $agency->logo_url;

        // SMTP Mail Settings
        $this->mail_mailer       = $agency->mail_mailer ?: 'smtp';
        $this->mail_host         = $agency->mail_host;
        $this->mail_port         = $agency->mail_port ? (int) $agency->mail_port : 587;
        $this->mail_username     = $agency->mail_username;
        $this->mail_password     = $agency->mail_password;
        $this->mail_encryption   = $agency->mail_encryption ?: 'tls';
        $this->mail_from_address = $agency->mail_from_address;
        $this->mail_from_name    = $agency->mail_from_name;
    }

    public function updatedIsSubjectToTva($value): void
    {
        if (!$value) {
            $this->tva_rate = 0.0;
        } elseif ($this->tva_rate == 0) {
            $this->tva_rate = 18.0;
        }
    }

    public function save(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $this->validate([
            'name'              => 'required|string|max:255',
            'legal_name'        => 'nullable|string|max:255',
            'manager_name'      => 'nullable|string|max:255',
            'manager_title'     => 'nullable|string|max:255',
            'manager_phone'     => 'nullable|string|max:50',
            'manager_id_card'   => 'nullable|string|max:255',
            'email'              => 'required|email|max:255',
            'phone'              => 'nullable|string|max:50',
            'address'            => 'nullable|string|max:255',
            'nif_rccm'           => 'nullable|string|max:100',
            'commission_rate'    => 'required|numeric|min:0|max:100',
            'is_subject_to_tva'  => 'boolean',
            'tva_rate'           => 'nullable|numeric|min:0|max:100',
            'logo'               => 'nullable|image|max:2048',

            'mail_mailer'       => 'nullable|string|in:smtp,sendmail,log',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        $agency = Agency::findOrFail($user->agency_id);

        if ($this->logo) {
            if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
                Storage::disk('public')->delete($agency->logo_path);
            }

            $path = $this->logo->store('agencies/logos', 'public');
            $agency->logo_path = $path;
        }

        $agency->name              = $this->name;
        $agency->legal_name         = $this->legal_name;
        $agency->manager_name      = $this->manager_name;
        $agency->manager_title     = $this->manager_title;
        $agency->manager_phone     = $this->manager_phone;
        $agency->manager_id_card   = $this->manager_id_card;
        $agency->email             = $this->email;
        $agency->phone             = $this->phone;
        $agency->address           = $this->address;
        $agency->nif_rccm          = $this->nif_rccm;
        $agency->commission_rate   = $this->commission_rate;
        $agency->is_subject_to_tva = $this->is_subject_to_tva;
        $agency->tva_rate          = $this->is_subject_to_tva ? ($this->tva_rate ?? 18.0) : 0.0;

        $agency->mail_mailer       = $this->mail_mailer;
        $agency->mail_host         = $this->mail_host;
        $agency->mail_port         = $this->mail_port;
        $agency->mail_username     = $this->mail_username;
        $agency->mail_password     = $this->mail_password;
        $agency->mail_encryption   = $this->mail_encryption;
        $agency->mail_from_address = $this->mail_from_address;
        $agency->mail_from_name    = $this->mail_from_name;

        $agency->save();

        $this->logo = null;
        $this->existingLogoUrl = $agency->fresh()->logo_url;

        session()->flash('message', 'Les informations et paramètres de votre agence ont été enregistrés avec succès !');
    }

    public function testMailConnection(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $agency = Agency::findOrFail($user->agency_id);

        // Appliquer temporairement les valeurs saisies à l'écran
        $tempAgency = clone $agency;
        $tempAgency->mail_mailer       = $this->mail_mailer;
        $tempAgency->mail_host         = $this->mail_host;
        $tempAgency->mail_port         = $this->mail_port;
        $tempAgency->mail_username     = $this->mail_username;
        $tempAgency->mail_password     = $this->mail_password;
        $tempAgency->mail_encryption   = $this->mail_encryption;
        $tempAgency->mail_from_address = $this->mail_from_address ?: $agency->email;
        $tempAgency->mail_from_name    = $this->mail_from_name ?: $agency->name;

        if (empty($tempAgency->mail_host)) {
            session()->flash('error', "Veuillez renseigner au moins l'hôte SMTP avant de tester la connexion.");
            return;
        }

        DynamicMailConfigurator::apply($tempAgency);

        try {
            Mail::raw("Ceci est un e-mail de test envoyé depuis votre application EasyImmob pour valider la configuration de votre serveur SMTP ({$tempAgency->mail_host}).", function ($message) use ($user, $tempAgency) {
                $message->to($user->email)
                    ->subject("Test de configuration SMTP — {$tempAgency->name}");
            });

            session()->flash('message', "E-mail de test envoyé avec succès à {$user->email} ! Votre serveur SMTP fonctionne parfaitement.");
        } catch (\Throwable $e) {
            session()->flash('error', "Échec de l'envoi du mail de test via SMTP ({$tempAgency->mail_host}:{$tempAgency->mail_port}) : " . $e->getMessage());
        }
    }

    public function removeLogo(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->agency_id) {
            return;
        }

        $agency = Agency::findOrFail($user->agency_id);

        if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
            Storage::disk('public')->delete($agency->logo_path);
        }

        $agency->logo_path = null;
        $agency->save();

        $this->existingLogoUrl = null;
        $this->logo = null;

        session()->flash('message', "Le logo de l'agence a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.agency.settings');
    }
}
