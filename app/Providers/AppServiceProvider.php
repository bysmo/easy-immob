<?php

namespace App\Providers;

use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Policies\ArrearPolicy;
use App\Domain\Deposit\Models\Deposit;
use App\Domain\Deposit\Policies\DepositPolicy;
use App\Domain\Incident\Models\Incident;
use App\Domain\Incident\Policies\IncidentPolicy;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Lease\Policies\LeasePolicy;
use App\Domain\Lease\Policies\LeaseTemplatePolicy;
use App\Domain\Owner\Models\Owner;
use App\Domain\Owner\Policies\OwnerPolicy;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Policies\PaymentPolicy;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Policies\PropertyPolicy;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\Tenant\Policies\TenantPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class AppServiceProvider extends AuthServiceProvider
{
    /**
     * Enregistrement explicite des policies (pas d'auto-discovery pour éviter
     * les surprises avec l'architecture modulaire).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Owner::class         => OwnerPolicy::class,
        Property::class      => PropertyPolicy::class,
        Tenant::class        => TenantPolicy::class,
        Lease::class         => LeasePolicy::class,
        LeaseTemplate::class => LeaseTemplatePolicy::class,
        Payment::class       => PaymentPolicy::class,
        Deposit::class       => DepositPolicy::class,
        Arrear::class        => ArrearPolicy::class,
        Incident::class      => IncidentPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Domain models live under App\Domain\{Module}\Models\{Model}, but their
        // factories are kept flat under Database\Factories to avoid mirroring the
        // domain folder structure. Resolve factories by model class basename only.
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }
}
