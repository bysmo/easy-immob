<?php

namespace App\Providers;

use App\Application\Actions\Auth\RegisterAgencyAction;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::createUsersUsing(RegisterAgencyAction::class);

        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe - ' . config('app.name', 'EasyImmob'))
                ->greeting('Bonjour' . (! empty($notifiable->name) ? ' ' . $notifiable->name : '') . ',')
                ->line('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
                ->action('Réinitialiser mon mot de passe', $url)
                ->line('Ce lien de réinitialisation de mot de passe expirera dans ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) . ' minutes.')
                ->line('Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune autre action n\'est requise.')
                ->salutation("Cordialement,\nL'équipe " . config('app.name', 'EasyImmob'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
