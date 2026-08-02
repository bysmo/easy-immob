<?php

use Illuminate\Support\Facades\Route;

// Racine → connexion
Route::get('/', fn () => redirect()->route('login'));

// Activation compte Bailleur (Signed URL 72h, pas besoin d'être connecté)
Route::get('/owner-portal/activate/{user}', \App\Livewire\OwnerPortal\Activate::class)
    ->middleware('signed')
    ->name('owner-portal.activate');

// Activation compte Locataire (Signed URL 72h, pas besoin d'être connecté)
Route::get('/tenant-portal/activate/{user}', \App\Livewire\TenantPortal\Activate::class)
    ->middleware('signed')
    ->name('tenant-portal.activate');

// Zone authentifiée
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isSuperAdmin()) {
            return redirect()->route('admin.saas-dashboard');
        }
        if ($user && $user->isOwner()) {
            return redirect()->route('owner-portal.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Profil & Sécurité
    Route::get('/profile', fn () => view('profile'))->name('profile.edit');

    // Catalogue & Recherche de biens (Réservé aux Locataires)
    Route::prefix('catalog')
        ->name('catalog.')
        ->group(function () {
            Route::get('/', function () {
                /** @var \App\Models\User $user */
                $user = \Illuminate\Support\Facades\Auth::user();
                if (!$user->isTenant()) {
                    return redirect()->route('dashboard');
                }
                return view('catalog.index');
            })->name('index');

            Route::get('/{propertyId}', function (int $propertyId) {
                return view('catalog.show', compact('propertyId'));
            })->name('show');
        });

    // Messagerie & Echanges Locataire - Agence
    Route::prefix('inquiries')
        ->name('inquiries.')
        ->group(function () {
            Route::get('/',            fn () => view('inquiries.index'))->name('index');
            Route::get('/{inquiryId}', fn (int $inquiryId) => view('inquiries.chat', compact('inquiryId')))->name('chat');
        });

    // Propriétaires
    Route::prefix('owners')
        ->name('owners.')
        ->middleware('can:owners.view')
        ->group(function () {
            Route::get('/',                     fn () => view('owners.index'))->name('index');
            Route::get('/create',               fn () => view('owners.create'))->name('create')->middleware('can:owners.create');
            Route::get('/payouts',              fn () => view('owners.payouts'))->name('payouts.index');
            Route::get('/payouts/{payoutId}/print', function (int $payoutId) {
                $payout = \App\Domain\Owner\Models\OwnerPayout::with(['owner', 'items.property', 'settlements'])->findOrFail($payoutId);
                return view('owners.payout-print', compact('payout'));
            })->name('payouts.print');
            Route::get('/{ownerId}',            fn (int $ownerId) => view('owners.edit', compact('ownerId')))->name('edit')->middleware('can:owners.update');
        });

    // Mandats de gestion
    Route::prefix('management-contracts')
        ->name('management-contracts.')
        ->middleware('can:owners.view')
        ->group(function () {
            Route::get('/', \App\Livewire\ManagementContracts\Index::class)->name('index');
            Route::get('/create', \App\Livewire\ManagementContracts\Create::class)->name('create');
            Route::get('/{contractId}', \App\Livewire\ManagementContracts\Show::class)->name('show');
            Route::get('/{contractId}/print', function (int $contractId) {
                $contract = \App\Domain\Owner\Models\ManagementContract::with(['agency', 'owner', 'properties'])->findOrFail($contractId);
                return view('management-contracts.print', compact('contract'));
            })->name('print');
        });

    // Biens immobiliers
    Route::prefix('properties')
        ->name('properties.')
        ->middleware('can:properties.view')
        ->group(function () {
            Route::get('/',             fn () => view('properties.index'))->name('index');
            Route::get('/create',       fn () => view('properties.create'))->name('create')->middleware('can:properties.create');
            Route::get('/{propertyId}', fn (int $propertyId) => view('properties.edit', compact('propertyId')))->name('edit')->middleware('can:properties.update');
        });

    // Incidents & Réparations
    Route::prefix('incidents')
        ->name('incidents.')
        ->group(function () {
            Route::get('/',           fn () => view('incidents.index'))->name('index');
            Route::get('/create',     fn () => view('incidents.create'))->name('create');
            Route::get('/{incidentId}', fn (int $incidentId) => view('incidents.show', compact('incidentId')))->name('show');
        });

    // Reporting & Rapports financiers
    Route::prefix('reports')
        ->name('reports.')
        ->middleware('can:reports.view')
        ->group(function () {
            Route::get('/', fn () => view('reports.index'))->name('index');
            Route::get('/owner-statements', fn () => view('reports.owner-statements'))->name('owner-statements');
            Route::get('/owner-statements/{ownerId}/print', function (
                int $ownerId,
                \Illuminate\Http\Request $request,
                \App\Domain\Report\Services\OwnerStatementService $service
            ) {
                $owner = \App\Domain\Owner\Models\Owner::where('id', $ownerId)->firstOrFail();
                $fee   = (float) $request->query('fee', 8.0);
                $period = $request->query('period');

                $statement = $service->generateStatement($owner, $fee, $period);

                return view('reports.owner-statement-print', compact('statement', 'period'));
            })->name('owner-statements.print');

            Route::get('/export/payments', function (\App\Domain\Report\Services\CsvExporter $exporter) {
                $payments = \App\Domain\Payment\Models\Payment::with(['rentSchedule.lease.tenant', 'rentSchedule.lease.property'])
                    ->latest()
                    ->get();

                $headers = ['Référence', 'Date', 'Locataire', 'Bien', 'Période', 'Montant (FCFA)', 'Mode de règlement'];

                $data = $payments->map(fn ($p) => [
                    'reference' => $p->reference,
                    'date'      => $p->payment_date?->format('d/m/Y'),
                    'tenant'    => $p->rentSchedule?->lease?->tenant?->full_name,
                    'property'  => $p->rentSchedule?->lease?->property?->title,
                    'period'    => $p->rentSchedule?->period,
                    'amount'    => (float) $p->amount,
                    'method'    => $p->payment_method?->label() ?? $p->payment_method,
                ])->toArray();

                return $exporter->download('export-encaissements-' . now()->format('Y-m-d') . '.csv', $headers, $data);
            })->name('export.payments');
        });

    // Impayés & Recouvrement
    Route::prefix('arrears')
        ->name('arrears.')
        ->middleware('can:arrears.view')
        ->group(function () {
            Route::get('/',          fn () => view('arrears.index'))->name('index');
            Route::get('/{arrearId}', fn (int $arrearId) => view('arrears.show', compact('arrearId')))->name('show');
        });

    // Centre de notifications
    Route::get('/notifications', fn () => view('notifications.index'))->name('notifications.index');

    // Loyers et encaissements
    Route::prefix('rents')
        ->name('rents.')
        ->middleware('can:rents.view')
        ->group(function () {
            Route::get('/', fn () => view('rents.index'))->name('index');
            Route::get('/receipts/{scheduleId}/print', function (int $scheduleId) {
                $schedule = \App\Domain\Rent\Models\RentSchedule::with(['lease.property.owner', 'lease.tenant', 'lease.agency'])
                    ->where('id', $scheduleId)
                    ->firstOrFail();
                return view('rents.receipt-print', compact('schedule'));
            })->name('receipt.print');
        });

    // Cautions & dépôts de garantie
    Route::prefix('deposits')
        ->name('deposits.')
        ->middleware('can:deposits.view')
        ->group(function () {
            Route::get('/', fn () => view('deposits.index'))->name('index');
        });

    // Contrats de location
    Route::prefix('leases')
        ->name('leases.')
        ->middleware('can:leases.view')
        ->group(function () {
            Route::get('/',           fn () => view('leases.index'))->name('index');
            Route::get('/create',     fn () => view('leases.create'))->name('create')->middleware('can:leases.create');
            Route::get('/{leaseId}',  fn (int $leaseId) => view('leases.show', compact('leaseId')))->name('show');
            Route::get('/{leaseId}/print', function (int $leaseId, \App\Domain\Lease\Services\LeaseContractGenerator $generator) {
                $lease = \App\Domain\Lease\Models\Lease::where('id', $leaseId)->firstOrFail();
                $content = $lease->template ? $generator->generate($lease, $lease->template->content) : 'Aucun contenu de contrat.';
                return view('leases.print', compact('lease', 'content'));
            })->name('print');
        });

    // Locataires
    Route::prefix('tenants')
        ->name('tenants.')
        ->middleware('can:tenants.view')
        ->group(function () {
            Route::get('/',           fn () => view('tenants.index'))->name('index');
            Route::get('/create',     fn () => view('tenants.create'))->name('create')->middleware('can:tenants.create');
            Route::get('/{tenantId}', fn (int $tenantId) => view('tenants.edit', compact('tenantId')))->name('edit')->middleware('can:tenants.update');
        });

    // Placeholder pour les modules non encore implémentés
    Route::get('/modules/{module}', fn (string $module) => view('coming-soon', compact('module')))
        ->name('modules.coming-soon');

    // Mon Abonnement & Configuration Agence (Agences Immobilières)
    Route::get('/agency/settings', \App\Livewire\Agency\Settings::class)->name('agency.settings');
    Route::get('/subscription', \App\Livewire\Subscription\Index::class)->name('subscription.index');

    // Administration des utilisateurs, référentiels & Espace SaaS Super Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Espace Admin SaaS (Réservé exclusivement au Super Admin SaaS)
        Route::middleware('can:saas.admin')->group(function () {
            Route::get('/saas-dashboard', \App\Livewire\Admin\SaasDashboard::class)->name('saas-dashboard');
            Route::get('/agencies', \App\Livewire\Admin\Agencies\Index::class)->name('agencies.index');
            Route::get('/saas-invoices', \App\Livewire\Admin\SaasInvoices\Index::class)->name('saas-invoices.index');
            Route::get('/plans', \App\Livewire\Admin\Plans\Index::class)->name('plans.index');
            Route::get('/mail-settings', \App\Livewire\Admin\MailSettings::class)->name('mail-settings.index');
            Route::get('/saas-invoices/{invoiceId}/print', function (int $invoiceId) {
                $invoice = \App\Domain\Subscription\Models\SaasInvoice::with(['agency', 'subscriptionPlan'])->findOrFail($invoiceId);
                return view('admin.saas-invoices.print', compact('invoice'));
            })->name('saas-invoices.print');
        });

        // Modèles de contrat
        Route::prefix('lease-templates')
            ->name('lease-templates.')
            ->middleware('can:leases.view')
            ->group(function () {
                Route::get('/', fn () => view('admin.lease-templates.index'))->name('index');
            });

        // Types de biens
        Route::prefix('property-types')
            ->name('property-types.')
            ->group(function () {
                Route::get('/', fn () => view('admin.property-types.index'))->name('index');
            });

        // Utilisateurs
        Route::prefix('users')
            ->name('users.')
            ->middleware('can:users.view')
            ->group(function () {
                Route::get('/',          fn () => view('admin.users.index'))->name('index');
                Route::get('/create',    fn () => view('admin.users.create'))->name('create')->middleware('can:users.create');
                Route::get('/{userId}',  fn (int $userId) => view('admin.users.edit', compact('userId')))->name('edit')->middleware('can:users.update');
            });
    });

    // ===================================================================
    // Portail Bailleur (espace privé propriétaire)
    // ===================================================================
    Route::prefix('owner-portal')
        ->name('owner-portal.')
        ->middleware('can:owner.portal.view')
        ->group(function () {
            Route::get('/',           \App\Livewire\OwnerPortal\Dashboard::class)->name('dashboard');
            Route::get('/properties', \App\Livewire\OwnerPortal\Properties::class)->name('properties');
            Route::get('/incidents',  \App\Livewire\OwnerPortal\Incidents::class)->name('incidents');
            Route::get('/financials', \App\Livewire\OwnerPortal\Financials::class)->name('financials');
            Route::get('/contracts',  \App\Livewire\OwnerPortal\Contracts::class)->name('contracts');
        });
});
