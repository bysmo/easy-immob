<?php

namespace App\Application\Services;

use App\Domain\Agency\Models\Agency;
use App\Domain\Subscription\Models\SaasSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class DynamicMailConfigurator
{
    /**
     * Applique les paramètres SMTP du Super Admin SaaS (ou de l'agence si surchargés).
     */
    public static function apply(?Agency $agency = null): void
    {
        if (Mail::getFacadeRoot() instanceof \Illuminate\Support\Testing\Fakes\MailFake) {
            return;
        }

        // 1. Vérifier si l'agence a des paramètres SMTP propres
        if ($agency && ! empty($agency->mail_host)) {
            $mailer     = $agency->mail_mailer ?: 'smtp';
            $host       = $agency->mail_host;
            $port       = (int) ($agency->mail_port ?: 587);
            $username   = $agency->mail_username;
            $password   = $agency->mail_password;
            $encryption = $agency->mail_encryption === 'none' ? null : ($agency->mail_encryption ?: 'tls');
            $fromAddr   = $agency->mail_from_address;
            $fromName   = $agency->mail_from_name;
        } else {
            // 2. Sinon, utiliser les paramètres configurés par le Super Admin SaaS
            $saasHost = SaasSetting::get('mail_host');
            if (empty($saasHost)) {
                return;
            }

            $mailer     = SaasSetting::get('mail_mailer', 'smtp');
            $host       = $saasHost;
            $port       = (int) (SaasSetting::get('mail_port', '587'));
            $username   = SaasSetting::get('mail_username');
            $password   = SaasSetting::get('mail_password');
            $encryption = SaasSetting::get('mail_encryption') === 'none' ? null : (SaasSetting::get('mail_encryption', 'tls'));
            $fromAddr   = SaasSetting::get('mail_from_address');
            $fromName   = SaasSetting::get('mail_from_name');
        }

        Config::set('mail.default', $mailer);
        Config::set('mail.mailers.smtp.transport', $mailer);
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);

        if (! empty($fromAddr)) {
            Config::set('mail.from.address', $fromAddr);
        }

        if (! empty($fromName)) {
            Config::set('mail.from.name', $fromName);
        }

        // Purge le cache du Mailer pour réinstancier avec la config dynamique (hors MailFake en test)
        if (! (Mail::getFacadeRoot() instanceof \Illuminate\Support\Testing\Fakes\MailFake)) {
            Mail::purge();
        }
    }
}
