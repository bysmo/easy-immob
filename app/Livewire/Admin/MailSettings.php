<?php

namespace App\Livewire\Admin;

use App\Application\Services\DynamicMailConfigurator;
use App\Domain\Subscription\Models\SaasSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class MailSettings extends Component
{
    public string $mail_mailer = 'smtp';
    public ?string $mail_host = null;
    public ?int $mail_port = 587;
    public ?string $mail_username = null;
    public ?string $mail_password = null;
    public ?string $mail_encryption = 'tls';
    public ?string $mail_from_address = null;
    public ?string $mail_from_name = null;

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement au Super Admin SaaS.');
        }

        $this->mail_mailer       = SaasSetting::get('mail_mailer', 'smtp');
        $this->mail_host         = SaasSetting::get('mail_host');
        $this->mail_port         = (int) (SaasSetting::get('mail_port', '587'));
        $this->mail_username     = SaasSetting::get('mail_username');
        $this->mail_password     = SaasSetting::get('mail_password');
        $this->mail_encryption   = SaasSetting::get('mail_encryption', 'tls');
        $this->mail_from_address = SaasSetting::get('mail_from_address', 'notifications@easyimmob.com');
        $this->mail_from_name    = SaasSetting::get('mail_from_name', 'EasyImmob SaaS Platform');
    }

    public function save(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isSuperAdmin()) {
            abort(403);
        }

        $this->validate([
            'mail_mailer'       => 'nullable|string|in:smtp,sendmail,log',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        SaasSetting::set('mail_mailer', $this->mail_mailer);
        SaasSetting::set('mail_host', $this->mail_host);
        SaasSetting::set('mail_port', (string) ($this->mail_port ?? 587));
        SaasSetting::set('mail_username', $this->mail_username);
        SaasSetting::set('mail_password', $this->mail_password);
        SaasSetting::set('mail_encryption', $this->mail_encryption);
        SaasSetting::set('mail_from_address', $this->mail_from_address);
        SaasSetting::set('mail_from_name', $this->mail_from_name);

        session()->flash('message', 'La configuration SMTP globale de la plateforme SaaS a été enregistrée avec succès !');
    }

    public function testMailConnection(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isSuperAdmin()) {
            return;
        }

        if (empty($this->mail_host)) {
            session()->flash('error', "Veuillez renseigner au moins l'hôte SMTP avant de tester la connexion.");
            return;
        }

        SaasSetting::set('mail_mailer', $this->mail_mailer);
        SaasSetting::set('mail_host', $this->mail_host);
        SaasSetting::set('mail_port', (string) ($this->mail_port ?? 587));
        SaasSetting::set('mail_username', $this->mail_username);
        SaasSetting::set('mail_password', $this->mail_password);
        SaasSetting::set('mail_encryption', $this->mail_encryption);
        SaasSetting::set('mail_from_address', $this->mail_from_address);
        SaasSetting::set('mail_from_name', $this->mail_from_name);

        DynamicMailConfigurator::apply();

        try {
            Mail::raw("Ceci est un e-mail de test envoyé depuis le panneau Super Admin de la plateforme EasyImmob SaaS pour valider la configuration du serveur SMTP global ({$this->mail_host}).", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject("Test SMTP Platform — EasyImmob Super Admin");
            });

            session()->flash('message', "E-mail de test envoyé avec succès à {$user->email} ! Votre serveur SMTP fonctionne parfaitement.");
        } catch (\Throwable $e) {
            session()->flash('error', "Échec de l'envoi du mail de test via SMTP ({$this->mail_host}:{$this->mail_port}) : " . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.mail-settings');
    }
}
