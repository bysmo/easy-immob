# Phase 1 — Fondations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the SaaS foundations for EasyImmob — multi-tenant agencies, self-service registration, authentication, roles/permissions, the app shell (sidebar/topbar/theme), and an empty-state dashboard — so later phases (owners, properties, tenants, leases...) can be built on top without touching this layer.

**Architecture:** Modular Monolith per `CLAUDE.md`. A new `Agency` domain (`app/Domain/Agency/`) is the tenancy root; a `BelongsToAgency` trait + Eloquent global scope (`app/Support/Tenancy/`) isolates every agency-scoped model's data; Spatie Laravel-Permission provides feature-level authorization (not data isolation); Laravel Fortify supplies the authentication contracts (`CreatesNewUsers`) and routes, with fully custom Livewire 3 views on top (Fortify's documented "headless" usage pattern) — form submissions are handled by the Livewire components themselves (`Auth::attempt`, hand-rolled rate limiting, and the `CreatesNewUsers` contract for registration), not by Fortify's own HTTP controllers.

**Tech Stack:** Laravel 13 (PHP 8.3), MySQL 8 (dev, `easy_immob` @ `127.0.0.1:3306`), SQLite in-memory (tests, already configured in `phpunit.xml`), Livewire 3, Alpine.js (bundled with Livewire), Tailwind CSS 4, Laravel Fortify, Spatie Laravel-Permission.

**Spec:** `docs/superpowers/specs/2026-07-26-phase1-fondations-design.md`

---

## Task 1: Install core packages

**Files:**
- Modify: `composer.json`, `composer.lock`

- [ ] **Step 1: Require the packages**

Run:
```bash
composer require laravel/fortify spatie/laravel-permission livewire/livewire
```
Expected: composer resolves and installs all three without conflicts (Laravel 13 / PHP 8.3 are all within their supported ranges).

- [ ] **Step 2: Publish Spatie's config and migration**

Run:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
Expected output includes:
```
Copying file [...] to [config/permission.php]
Copying file [...] to [database/migrations/..._create_permission_tables.php]
```

- [ ] **Step 3: Publish Fortify's config**

Run:
```bash
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```
Expected: `config/fortify.php` created and a `..._create_two_factor_columns_for_users_table.php` (or similar) migration published — this migration is not used in this lot (2FA is out of scope) and will be deleted in Task 5.

- [ ] **Step 4: Register Spatie's middleware aliases**

Modify `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
```

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock config/permission.php config/fortify.php database/migrations bootstrap/app.php
git commit -m "Install Fortify, Spatie Permission and Livewire"
```

---

## Task 2: Agency model and migration

**Files:**
- Create: `database/migrations/2026_07_26_000001_create_agencies_table.php`
- Create: `app/Domain/Agency/Models/Agency.php`
- Test: `tests/Feature/Domain/Agency/AgencyTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Domain/Agency/AgencyTest.php`:

```php
<?php

namespace Tests\Feature\Domain\Agency;

use App\Domain\Agency\Models\Agency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created_with_expected_attributes(): void
    {
        $agency = Agency::create([
            'name' => 'Agence du Plateau',
            'legal_name' => 'Agence du Plateau SARL',
            'email' => 'contact@plateau.example',
            'phone' => '+225 07 00 00 00 00',
            'address' => 'Abidjan, Plateau',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Agence du Plateau',
            'status' => 'active',
        ]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AgencyTest`
Expected: FAIL — `Class "App\Domain\Agency\Models\Agency" not found`.

- [ ] **Step 3: Create the migration**

Create `database/migrations/2026_07_26_000001_create_agencies_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
```

- [ ] **Step 4: Create the Agency model**

Create `app/Domain/Agency/Models/Agency.php`:

```php
<?php

namespace App\Domain\Agency\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'legal_name', 'email', 'phone', 'address', 'status'])]
class Agency extends Model
{
    use HasFactory;
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=AgencyTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_26_000001_create_agencies_table.php app/Domain/Agency/Models/Agency.php tests/Feature/Domain/Agency/AgencyTest.php
git commit -m "Add Agency model and migration"
```

---

## Task 3: agency_id on users + BelongsToAgency tenancy scope

**Files:**
- Create: `database/migrations/2026_07_26_000002_add_agency_id_to_users_table.php`
- Create: `app/Support/Tenancy/BelongsToAgency.php`
- Create: `app/Support/Tenancy/AgencyScope.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/Support/Tenancy/AgencyScopeTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Support/Tenancy/AgencyScopeTest.php`:

```php
<?php

namespace Tests\Feature\Support\Tenancy;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_scoped_to_the_authenticated_users_agency(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $userA = User::factory()->for($agencyA, 'agency')->create();
        User::factory()->for($agencyB, 'agency')->create();

        $this->actingAs($userA);

        $this->assertCount(1, User::all());
        $this->assertTrue(User::first()->is($userA));
    }

    public function test_new_records_are_stamped_with_the_authenticated_users_agency(): void
    {
        $agency = Agency::factory()->create();
        $userA = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($userA);

        $created = User::create([
            'name' => 'Nouveau Collègue',
            'email' => 'collegue@example.com',
            'password' => 'password',
        ]);

        $this->assertSame($agency->id, $created->agency_id);
    }

    public function test_without_an_authenticated_user_no_scope_is_applied(): void
    {
        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        User::factory()->for($agencyA, 'agency')->create();
        User::factory()->for($agencyB, 'agency')->create();

        $this->assertCount(2, User::all());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AgencyScopeTest`
Expected: FAIL — `agency_id` column / `agency` relation does not exist yet.

- [ ] **Step 3: Add an Agency factory**

Create `database/factories/AgencyFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agency>
 */
class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company().' SARL',
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'status' => 'active',
        ];
    }
}
```

Add `HasFactory` support by pointing the model at it — already covered since `HasFactory` resolves `Database\Factories\AgencyFactory` by convention (namespace `App\Domain\Agency\Models` → factory name `AgencyFactory`, which Laravel's factory discovery finds by class basename, not namespace, so no extra step is needed).

- [ ] **Step 4: Create the migration**

Create `database/migrations/2026_07_26_000002_add_agency_id_to_users_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agency_id')
                ->after('id')
                ->constrained()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
        });
    }
};
```

- [ ] **Step 5: Create the tenancy scope**

Create `app/Support/Tenancy/AgencyScope.php`:

```php
<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AgencyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $builder->where($model->qualifyColumn('agency_id'), Auth::user()->agency_id);
        }
    }
}
```

- [ ] **Step 6: Create the trait**

Create `app/Support/Tenancy/BelongsToAgency.php`:

```php
<?php

namespace App\Support\Tenancy;

use App\Domain\Agency\Models\Agency;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToAgency
{
    public static function bootBelongsToAgency(): void
    {
        static::addGlobalScope(new AgencyScope);

        static::creating(function ($model) {
            if ($model->agency_id === null && Auth::check()) {
                $model->agency_id = Auth::user()->agency_id;
            }
        });
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
```

- [ ] **Step 7: Apply the trait to User**

Modify `app/Models/User.php`:

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['agency_id', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use BelongsToAgency, HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

- [ ] **Step 8: Update the User factory to always attach an agency**

Modify `database/factories/UserFactory.php` — add an `agency_id` default that creates one on demand:

```php
<?php

namespace Database\Factories;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

- [ ] **Step 9: Run test to verify it passes**

Run: `php artisan test --filter=AgencyScopeTest`
Expected: PASS (all 3 assertions)

- [ ] **Step 10: Commit**

```bash
git add database/migrations/2026_07_26_000002_add_agency_id_to_users_table.php app/Support/Tenancy database/factories/AgencyFactory.php database/factories/UserFactory.php app/Models/User.php tests/Feature/Support/Tenancy/AgencyScopeTest.php
git commit -m "Add multi-tenant data isolation via BelongsToAgency"
```

---

## Task 4: Roles & permissions seeder

**Files:**
- Create: `database/seeders/RolesAndPermissionsSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/Database/Seeders/RolesAndPermissionsSeederTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Database/Seeders/RolesAndPermissionsSeederTest.php`:

```php
<?php

namespace Tests\Feature\Database\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_six_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(
            ['Administrateur', 'Gestionnaire', 'Comptable', 'Agent', 'Propriétaire', 'Locataire'],
            Role::pluck('name')->all(),
        );
    }

    public function test_administrateur_has_every_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::findByName('Administrateur');

        $this->assertSame(Permission::count(), $admin->permissions()->count());
    }

    public function test_gestionnaire_cannot_manage_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $gestionnaire = Role::findByName('Gestionnaire');

        $this->assertFalse($gestionnaire->hasPermissionTo('users.manage-roles'));
        $this->assertTrue($gestionnaire->hasPermissionTo('properties.view'));
        $this->assertTrue($gestionnaire->hasPermissionTo('arrears.manage'));
    }

    public function test_proprietaire_and_locataire_have_no_internal_permissions_yet(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(0, Role::findByName('Propriétaire')->permissions()->count());
        $this->assertSame(0, Role::findByName('Locataire')->permissions()->count());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=RolesAndPermissionsSeederTest`
Expected: FAIL — `Class "Database\Seeders\RolesAndPermissionsSeeder" not found`.

- [ ] **Step 3: Write the seeder**

Create `database/seeders/RolesAndPermissionsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * All permissions in the system, grouped by domain (docs/03-ROLES-ET-PERMISSIONS.md).
     *
     * @var array<int, string>
     */
    private const PERMISSIONS = [
        'users.view', 'users.create', 'users.update', 'users.delete', 'users.manage-roles',
        'owners.view', 'owners.create', 'owners.update', 'owners.delete',
        'properties.view', 'properties.create', 'properties.update', 'properties.delete',
        'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete',
        'leases.view', 'leases.create', 'leases.update', 'leases.delete',
        'rents.view', 'rents.record-payment',
        'deposits.view', 'deposits.manage',
        'arrears.view', 'arrears.manage',
        'notifications.view',
        'documents.view', 'documents.upload',
        'reports.view',
        'audit.view',
    ];

    /**
     * Permissions granted per role, beyond Administrateur (which gets everything).
     *
     * @var array<string, array<int, string>>
     */
    private const ROLE_PERMISSIONS = [
        'Gestionnaire' => [
            'owners.view', 'owners.create', 'owners.update', 'owners.delete',
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'tenants.view', 'tenants.create', 'tenants.update', 'tenants.delete',
            'leases.view', 'leases.create', 'leases.update', 'leases.delete',
            'rents.view', 'rents.record-payment',
            'arrears.view', 'arrears.manage',
        ],
        'Comptable' => [
            'rents.view', 'rents.record-payment',
            'deposits.view', 'deposits.manage',
            'documents.view',
            'reports.view',
        ],
        'Agent' => [
            'properties.view', 'properties.create', 'properties.update', 'properties.delete',
            'owners.view',
        ],
        'Propriétaire' => [],
        'Locataire' => [],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission);
        }

        $administrateur = Role::findOrCreate('Administrateur');
        $administrateur->syncPermissions(self::PERMISSIONS);

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            Role::findOrCreate($roleName)->syncPermissions($permissions);
        }
    }
}
```

- [ ] **Step 4: Wire it into DatabaseSeeder**

Modify `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=RolesAndPermissionsSeederTest`
Expected: PASS (all 4 tests)

- [ ] **Step 6: Commit**

```bash
git add database/seeders tests/Feature/Database/Seeders/RolesAndPermissionsSeederTest.php
git commit -m "Seed the 6 roles and their permissions"
```

---

## Task 5: Agency self-registration action

**Files:**
- Create: `app/Application/Actions/Auth/RegisterAgencyAction.php`
- Delete: the two-factor-columns migration published by Fortify in Task 1 (not used this lot)
- Test: `tests/Feature/Application/Actions/Auth/RegisterAgencyActionTest.php`

- [ ] **Step 1: Delete the unused 2FA migration**

Run:
```bash
rm database/migrations/*_add_two_factor_columns_to_users_table.php
```
(2FA is explicitly out of scope for this lot — see spec §4. If Fortify's publish in Task 1 did not create this file, skip this step.)

- [ ] **Step 2: Write the failing test**

Create `tests/Feature/Application/Actions/Auth/RegisterAgencyActionTest.php`:

```php
<?php

namespace Tests\Feature\Application\Actions\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAgencyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_agency_and_its_first_administrator(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = (new RegisterAgencyAction)->create([
            'agency_name' => 'Agence du Plateau',
            'name' => 'Awa Konan',
            'email' => 'awa@plateau.example',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('agencies', ['name' => 'Agence du Plateau']);
        $this->assertSame($user->agency_id, $user->fresh()->agency->id);
        $this->assertTrue($user->fresh()->hasRole('Administrateur'));
    }

    public function test_it_rejects_a_weak_password(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        (new RegisterAgencyAction)->create([
            'agency_name' => 'Agence du Plateau',
            'name' => 'Awa Konan',
            'email' => 'awa@plateau.example',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
    }
}
```

- [ ] **Step 3: Run test to verify it fails**

Run: `php artisan test --filter=RegisterAgencyActionTest`
Expected: FAIL — `Class "App\Application\Actions\Auth\RegisterAgencyAction" not found`.

- [ ] **Step 4: Write the action**

Create `app/Application/Actions/Auth/RegisterAgencyAction.php`:

```php
<?php

namespace App\Application\Actions\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisterAgencyAction implements CreatesNewUsers
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'agency_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $agency = Agency::create([
                'name' => $input['agency_name'],
                'email' => $input['email'],
                'status' => 'active',
            ]);

            $user = User::create([
                'agency_id' => $agency->id,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->assignRole('Administrateur');

            return $user;
        });
    }
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=RegisterAgencyActionTest`
Expected: PASS (both tests)

- [ ] **Step 6: Commit**

```bash
git add app/Application/Actions/Auth/RegisterAgencyAction.php tests/Feature/Application/Actions/Auth/RegisterAgencyActionTest.php database/migrations
git commit -m "Add agency self-registration action"
```

---

## Task 6: Fortify wiring (headless)

**Files:**
- Create: `app/Providers/FortifyServiceProvider.php`
- Modify: `bootstrap/providers.php`, `config/fortify.php`

- [ ] **Step 1: Configure Fortify's enabled features**

Modify `config/fortify.php` — find the `'features'` array and set it to:

```php
    'features' => [
        Features::registration(),
        Features::resetPasswords(),
    ],
```

(Leave every other key in the published file untouched — email verification and 2FA stay commented out / absent, per spec §4.)

- [ ] **Step 2: Create the Fortify service provider**

Create `app/Providers/FortifyServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Application\Actions\Auth\RegisterAgencyAction;
use Illuminate\Support\ServiceProvider;
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
    }
}
```

- [ ] **Step 3: Register the provider**

Modify `bootstrap/providers.php`:

```php
<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
];
```

- [ ] **Step 4: Verify routes are registered**

Run: `php artisan route:list --name=login`
Expected: a `GET|HEAD login` route and a `POST login` route both appear (Fortify's own routes — `POST login`/`POST register` stay registered but are not used by our Livewire forms, which call `Auth::attempt`/the `CreatesNewUsers` contract directly; see Task 8/9). This is expected and matches Fortify's documented "headless" usage.

This step has no test of its own — the views it references (`auth.login`, etc.) do not exist yet and will 404/error if hit; that's expected until Task 7–9 create them. No commit yet — this task's files are committed together with Task 7's UI kit to keep the provider and its first consumer in the same reviewable change.

- [ ] **Step 5: Commit**

```bash
git add app/Providers/FortifyServiceProvider.php bootstrap/providers.php config/fortify.php
git commit -m "Configure Fortify as a headless auth backend"
```

---

## Task 7: UI component kit + layouts + theme

**Files:**
- Create: `resources/views/components/button.blade.php`
- Create: `resources/views/components/input.blade.php`
- Create: `resources/views/components/label.blade.php`
- Create: `resources/views/components/card.blade.php`
- Create: `resources/views/components/badge.blade.php`
- Create: `resources/views/components/layouts/guest.blade.php`
- Create: `resources/views/components/layouts/app.blade.php`
- Create: `app/Support/Navigation/SidebarMenu.php`
- Modify: `resources/css/app.css`
- Modify: `tests/TestCase.php`
- Test: `tests/Feature/Ui/GuestLayoutTest.php`

- [ ] **Step 1: Disable Vite manifest lookups in tests**

Every layout in this task renders `@vite(...)`. Without a compiled `public/build/manifest.json` (no `npm run build` has run), that directive throws `Illuminate\Foundation\Vite`'s manifest-not-found exception in every test that renders a full page — not just here, but in every later task's HTTP/Livewire tests too. Fix it once, globally, now.

Modify `tests/TestCase.php`:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
```

- [ ] **Step 2: Write the failing test**

Create `tests/Feature/Ui/GuestLayoutTest.php`:

```php
<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    public function test_guest_layout_renders_the_agency_name_and_slot(): void
    {
        $html = Blade::render(
            '<x-layouts.guest>Contenu de test</x-layouts.guest>'
        );

        $this->assertStringContainsString('EasyImmob', $html);
        $this->assertStringContainsString('Contenu de test', $html);
    }

    public function test_button_component_renders_its_slot(): void
    {
        $html = Blade::render('<x-button>Valider</x-button>');

        $this->assertStringContainsString('Valider', $html);
        $this->assertStringContainsString('<button', $html);
    }
}
```

- [ ] **Step 3: Run test to verify it fails**

Run: `php artisan test --filter=GuestLayoutTest`
Expected: FAIL — view `layouts.guest` / component `button` not found.

- [ ] **Step 4: Set the Tailwind theme**

Modify `resources/css/app.css`:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';

@custom-variant dark (&:where(.dark, .dark *));

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
    --color-primary-50: #ecfdf5;
    --color-primary-100: #d1fae5;
    --color-primary-500: #10b981;
    --color-primary-600: #059669;
    --color-primary-700: #047857;
}
```

- [ ] **Step 5: Create the button component**

Create `resources/views/components/button.blade.php`:

```blade
@props(['variant' => 'primary', 'type' => 'submit'])

@php
$classes = match ($variant) {
    'primary' => 'bg-primary-600 hover:bg-primary-700 text-white',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600',
    default => 'bg-primary-600 hover:bg-primary-700 text-white',
};
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed $classes"]) }}>
    {{ $slot }}
</button>
```

- [ ] **Step 6: Create the input and label components**

Create `resources/views/components/label.blade.php`:

```blade
<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1']) }}>
    {{ $slot }}
</label>
```

Create `resources/views/components/input.blade.php`:

```blade
@props(['error' => null])

<input {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm']) }}>
@if ($error)
    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
@endif
```

- [ ] **Step 7: Create the card and badge components**

Create `resources/views/components/card.blade.php`:

```blade
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6']) }}>
    {{ $slot }}
</div>
```

Create `resources/views/components/badge.blade.php`:

```blade
@props(['color' => 'gray'])

@php
$classes = match ($color) {
    'green' => 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-100',
    'red' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100',
    'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100',
    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
```

- [ ] **Step 8: Create the guest layout**

Create `resources/views/components/layouts/guest.blade.php`:

```blade
<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="$watch('dark', v => localStorage.setItem('dark', v))" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <span class="text-2xl font-semibold text-primary-700 dark:text-primary-400">EasyImmob</span>
        </div>
        <x-card>
            {{ $slot }}
        </x-card>
    </div>
    @livewireScripts
</body>
</html>
```

- [ ] **Step 9: Create the app layout (sidebar + topbar)**

Create `resources/views/components/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true', sidebarOpen: false }" x-init="$watch('dark', v => localStorage.setItem('dark', v))" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex">
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center px-6 text-xl font-semibold text-primary-700 dark:text-primary-400">
                EasyImmob
            </div>
            <nav class="px-3 space-y-1">
                @foreach (\App\Support\Navigation\SidebarMenu::items() as $item)
                    <a href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route'], $item['params']) : '#' }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700">
                        <span>{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 lg:ml-64">
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6">
                <button class="lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Ouvrir le menu">☰</button>
                <input type="search" disabled placeholder="Recherche (bientôt disponible)"
                       class="hidden md:block w-72 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm text-gray-400">
                <div class="flex items-center gap-4">
                    <button @click="dark = !dark" aria-label="Basculer le thème">🌓</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 dark:text-gray-300 hover:text-red-600">
                            {{ auth()->user()->name }} — Déconnexion
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
```

This references `App\Support\Navigation\SidebarMenu`, created next. The `Route::has()` guard means entries whose route isn't registered yet (e.g. `modules.coming-soon`, wired in Task 13) safely render as `#` instead of throwing `RouteNotFoundException` — this layout is exercised as early as Task 11 (Admin screens use it as their default Livewire layout), well before Task 13 lands.

- [ ] **Step 10: Create the sidebar menu definition**

Create `app/Support/Navigation/SidebarMenu.php`:

```php
<?php

namespace App\Support\Navigation;

class SidebarMenu
{
    /**
     * @return array<int, array{label: string, icon: string, route: string, params: array<string, mixed>}>
     */
    public static function items(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => '📊', 'route' => 'dashboard', 'params' => []],
            ['label' => 'Propriétaires', 'icon' => '👤', 'route' => 'modules.coming-soon', 'params' => ['module' => 'proprietaires']],
            ['label' => 'Biens', 'icon' => '🏠', 'route' => 'modules.coming-soon', 'params' => ['module' => 'biens']],
            ['label' => 'Locataires', 'icon' => '🧑', 'route' => 'modules.coming-soon', 'params' => ['module' => 'locataires']],
            ['label' => 'Contrats', 'icon' => '📄', 'route' => 'modules.coming-soon', 'params' => ['module' => 'contrats']],
            ['label' => 'Loyers', 'icon' => '💰', 'route' => 'modules.coming-soon', 'params' => ['module' => 'loyers']],
            ['label' => 'Cautions', 'icon' => '🔒', 'route' => 'modules.coming-soon', 'params' => ['module' => 'cautions']],
            ['label' => 'Impayés', 'icon' => '⚠️', 'route' => 'modules.coming-soon', 'params' => ['module' => 'impayes']],
            ['label' => 'Notifications', 'icon' => '🔔', 'route' => 'modules.coming-soon', 'params' => ['module' => 'notifications']],
            ['label' => 'Rapports', 'icon' => '📈', 'route' => 'modules.coming-soon', 'params' => ['module' => 'rapports']],
            ['label' => 'Administration', 'icon' => '⚙️', 'route' => 'admin.users.index', 'params' => []],
        ];
    }
}
```

`admin.users.index` and `modules.coming-soon` do not exist yet at this point in the plan (Tasks 11 and 13) — the `Route::has()` guard in the layout means that is harmless; both resolve to real links once their tasks land.

- [ ] **Step 11: Run test to verify it passes**

Run: `php artisan test --filter=GuestLayoutTest`
Expected: PASS (both tests)

- [ ] **Step 12: Commit**

```bash
git add resources/views/components resources/css/app.css app/Support/Navigation/SidebarMenu.php tests/TestCase.php tests/Feature/Ui/GuestLayoutTest.php
git commit -m "Add UI component kit, guest/app layouts, emerald theme and sidebar menu"
```

---

## Task 8: Login

**Files:**
- Create: `app/Livewire/Auth/Login.php`
- Create: `resources/views/livewire/auth/login.blade.php`
- Create: `resources/views/auth/login.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Auth/LoginTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Auth/LoginTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_log_in_with_correct_credentials(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', $user->email)
                ->set('password', 'wrong-password')
                ->call('authenticate');
        }

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=LoginTest`
Expected: FAIL — `Class "App\Livewire\Auth\Login" not found`.

- [ ] **Step 3: Write the Livewire component**

Create `app/Livewire/Auth/Login.php`:

```php
<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public function authenticate()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez dans quelques minutes.',
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
```

- [ ] **Step 4: Write the Livewire view**

Create `resources/views/livewire/auth/login.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Connexion</h1>

    <form wire:submit="authenticate" class="space-y-4">
        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" autofocus />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="text-primary-700 dark:text-primary-400">Mot de passe oublié ?</a>
            <a href="{{ route('register') }}" class="text-primary-700 dark:text-primary-400">Créer une agence</a>
        </div>

        <x-button class="w-full">Se connecter</x-button>
    </form>
</div>
```

- [ ] **Step 5: Wire the auth.login blade view (already targeted by Fortify::loginView in Task 6)**

Create `resources/views/auth/login.blade.php`:

```blade
<x-layouts.guest title="Connexion — EasyImmob">
    <livewire:auth.login />
</x-layouts.guest>
```

- [ ] **Step 6: Add the dashboard placeholder route** (needed for `route('dashboard')` to resolve; the real Dashboard component lands in Task 13)

Modify `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => 'dashboard placeholder')->name('dashboard');
});
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=LoginTest`
Expected: PASS (all 3 tests)

- [ ] **Step 8: Commit**

```bash
git add app/Livewire/Auth/Login.php resources/views/livewire/auth/login.blade.php resources/views/auth/login.blade.php routes/web.php tests/Feature/Auth/LoginTest.php
git commit -m "Add login with Livewire and hand-rolled rate limiting"
```

---

## Task 9: Registration, forgot password, reset password, logout

**Files:**
- Create: `app/Livewire/Auth/Register.php`, `resources/views/livewire/auth/register.blade.php`, `resources/views/auth/register.blade.php`
- Create: `app/Livewire/Auth/ForgotPassword.php`, `resources/views/livewire/auth/forgot-password.blade.php`, `resources/views/auth/forgot-password.blade.php`
- Create: `app/Livewire/Auth/ResetPassword.php`, `resources/views/livewire/auth/reset-password.blade.php`, `resources/views/auth/reset-password.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Auth/RegistrationTest.php`, `tests/Feature/Auth/PasswordResetTest.php`, `tests/Feature/Auth/LogoutTest.php`

- [ ] **Step 1: Write the failing registration test**

Create `tests/Feature/Auth/RegistrationTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_the_form_creates_an_agency_and_logs_the_admin_in(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        Livewire::test(Register::class)
            ->set('agencyName', 'Agence du Plateau')
            ->set('name', 'Awa Konan')
            ->set('email', 'awa@plateau.example')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('agencies', ['name' => 'Agence du Plateau']);
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('Administrateur'));
    }

    public function test_password_confirmation_mismatch_is_rejected(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        Livewire::test(Register::class)
            ->set('agencyName', 'Agence du Plateau')
            ->set('name', 'Awa Konan')
            ->set('email', 'awa@plateau.example')
            ->set('password', 'Password123')
            ->set('password_confirmation', 'Different123')
            ->call('register')
            ->assertHasErrors(['password']);

        $this->assertGuest();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=RegistrationTest`
Expected: FAIL — `Class "App\Livewire\Auth\Register" not found`.

- [ ] **Step 3: Write the Register Livewire component**

Create `app/Livewire/Auth/Register.php`:

```php
<?php

namespace App\Livewire\Auth;

use App\Application\Actions\Auth\RegisterAgencyAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public string $agencyName = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(RegisterAgencyAction $action)
    {
        $user = $action->create([
            'agency_name' => $this->agencyName,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
```

`RegisterAgencyAction::create()` already validates and throws `ValidationException` on bad input (Task 5) — Livewire automatically turns that into `$errors` on the component, so `assertHasErrors` in the test above works without extra wiring.

- [ ] **Step 4: Write the Livewire view**

Create `resources/views/livewire/auth/register.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Créer votre agence</h1>

    <form wire:submit="register" class="space-y-4">
        <div>
            <x-label for="agencyName">Nom de l'agence</x-label>
            <x-input wire:model="agencyName" id="agencyName" autofocus />
            @error('agency_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="name">Votre nom</x-label>
            <x-input wire:model="name" id="name" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" />
        </div>

        <div class="text-sm">
            <a href="{{ route('login') }}" class="text-primary-700 dark:text-primary-400">Déjà un compte ? Se connecter</a>
        </div>

        <x-button class="w-full">Créer mon agence</x-button>
    </form>
</div>
```

- [ ] **Step 5: Wire the auth.register blade view**

Create `resources/views/auth/register.blade.php`:

```blade
<x-layouts.guest title="Créer une agence — EasyImmob">
    <livewire:auth.register />
</x-layouts.guest>
```

- [ ] **Step 6: Run the registration test**

Run: `php artisan test --filter=RegistrationTest`
Expected: PASS (both tests)

- [ ] **Step 7: Write the failing password-reset test**

Create `tests/Feature/Auth/PasswordResetTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_reset_link_sends_a_notification(): void
    {
        Notification::fake();

        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_a_valid_token_resets_the_password(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $token = \Illuminate\Support\Facades\Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'NewPassword123')
            ->set('password_confirmation', 'NewPassword123')
            ->call('resetPassword')
            ->assertRedirect(route('login'));

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword123', $user->fresh()->password));
    }
}
```

- [ ] **Step 8: Run test to verify it fails**

Run: `php artisan test --filter=PasswordResetTest`
Expected: FAIL — `Class "App\Livewire\Auth\ForgotPassword" not found`.

- [ ] **Step 9: Write the ForgotPassword component**

Create `app/Livewire/Auth/ForgotPassword.php`:

```php
<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public string $status = '';

    public function sendResetLink()
    {
        $this->validate();

        Password::sendResetLink(['email' => $this->email]);

        $this->status = 'Si cet email existe, un lien de réinitialisation vient d\'être envoyé.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
```

Note: the status message is intentionally identical whether or not the email exists (no `assertHasNoErrors` failure either way) — this avoids leaking which emails are registered.

- [ ] **Step 10: Write the ForgotPassword view**

Create `resources/views/livewire/auth/forgot-password.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mot de passe oublié</h1>

    @if ($status)
        <p class="mb-4 text-sm text-primary-700 dark:text-primary-400">{{ $status }}</p>
    @endif

    <form wire:submit="sendResetLink" class="space-y-4">
        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" autofocus />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <x-button class="w-full">Envoyer le lien de réinitialisation</x-button>
    </form>
</div>
```

Create `resources/views/auth/forgot-password.blade.php`:

```blade
<x-layouts.guest title="Mot de passe oublié — EasyImmob">
    <livewire:auth.forgot-password />
</x-layouts.guest>
```

- [ ] **Step 11: Write the ResetPassword component**

Create `app/Livewire/Auth/ResetPassword.php`:

```php
<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user) {
                $user->forceFill(['password' => Hash::make($this->password)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
```

- [ ] **Step 12: Write the ResetPassword view and blade wrapper**

Create `resources/views/livewire/auth/reset-password.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Réinitialiser le mot de passe</h1>

    <form wire:submit="resetPassword" class="space-y-4">
        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" autofocus />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Nouveau mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" />
        </div>

        <x-button class="w-full">Réinitialiser</x-button>
    </form>
</div>
```

Create `resources/views/auth/reset-password.blade.php`:

```blade
<x-layouts.guest title="Réinitialiser le mot de passe — EasyImmob">
    <livewire:auth.reset-password :token="$request->route('token')" />
</x-layouts.guest>
```

- [ ] **Step 13: Run the password reset test**

Run: `php artisan test --filter=PasswordResetTest`
Expected: PASS (both tests)

- [ ] **Step 14: Write the failing logout test**

Create `tests/Feature/Auth/LogoutTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_logged_in_user_can_log_out(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
```

- [ ] **Step 15: Run test to verify it fails**

Run: `php artisan test --filter=LogoutTest`
Expected: FAIL — no `/logout` route responds as expected yet (Fortify registers it, but confirm behavior).

If this already passes (Fortify's own logout route is active from Task 1), skip to Step 17 — Fortify's default logout action is sufficient and needs no custom code.

- [ ] **Step 16: Fix routing if needed**

Fortify registers `POST /logout` by default; if the test fails because `/` doesn't redirect to `login`, adjust `routes/web.php`'s root route (already redirects to `login` since Task 8, Step 6). No new code should be required here.

- [ ] **Step 17: Run test to verify it passes**

Run: `php artisan test --filter=LogoutTest`
Expected: PASS

- [ ] **Step 18: Commit**

```bash
git add app/Livewire/Auth resources/views/livewire/auth resources/views/auth tests/Feature/Auth
git commit -m "Add registration, password reset and logout"
```

---

## Task 10: Audit logging

**Files:**
- Create: `database/migrations/2026_07_26_000003_create_audit_logs_table.php`
- Create: `app/Support/Audit/Models/AuditLog.php`
- Create: `app/Support/Audit/AuditLogger.php`
- Create: `app/Support/Audit/Listeners/LogAuthenticationActivity.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Support/Audit/AuditLoggerTest.php`, `tests/Feature/Support/Audit/AuthenticationAuditTest.php`

- [ ] **Step 1: Write the failing AuditLogger test**

Create `tests/Feature/Support/Audit/AuditLoggerTest.php`:

```php
<?php

namespace Tests\Feature\Support\Audit;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_an_action_for_the_authenticated_user(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();
        $this->actingAs($user);

        app(AuditLogger::class)->log('user.created', $user, [], ['name' => $user->name]);

        $this->assertDatabaseHas('audit_logs', [
            'agency_id' => $agency->id,
            'user_id' => $user->id,
            'action' => 'user.created',
            'entity_type' => User::class,
            'entity_id' => $user->id,
        ]);
    }

    public function test_it_accepts_an_explicit_agency_and_user_for_unauthenticated_contexts(): void
    {
        $agency = Agency::factory()->create();

        app(AuditLogger::class)->log('auth.failed', agencyId: $agency->id, userId: null);

        $this->assertDatabaseHas('audit_logs', [
            'agency_id' => $agency->id,
            'user_id' => null,
            'action' => 'auth.failed',
        ]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=AuditLoggerTest`
Expected: FAIL — `Class "App\Support\Audit\AuditLogger" not found`.

- [ ] **Step 3: Create the migration**

Create `database/migrations/2026_07_26_000003_create_audit_logs_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
```

- [ ] **Step 4: Create the AuditLog model**

Create `app/Support/Audit/Models/AuditLog.php`:

```php
<?php

namespace App\Support\Audit\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['agency_id', 'user_id', 'action', 'entity_type', 'entity_id', 'old_values', 'new_values', 'ip_address', 'user_agent'])]
class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }
}
```

- [ ] **Step 5: Create the AuditLogger service**

Create `app/Support/Audit/AuditLogger.php`:

```php
<?php

namespace App\Support\Audit;

use App\Support\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    public function log(
        string $action,
        ?Model $entity = null,
        array $old = [],
        array $new = [],
        ?int $agencyId = null,
        ?int $userId = null,
    ): AuditLog {
        return AuditLog::create([
            'agency_id' => $agencyId ?? Auth::user()?->agency_id,
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=AuditLoggerTest`
Expected: PASS (both tests)

- [ ] **Step 7: Write the failing authentication-events test**

Create `tests/Feature/Support/Audit/AuthenticationAuditTest.php`:

```php
<?php

namespace Tests\Feature\Support\Audit;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use App\Livewire\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_successful_login_is_audited(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Password123')
            ->call('authenticate');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.login',
        ]);
    }

    public function test_a_failed_login_is_audited(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create([
            'password' => Hash::make('Password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('authenticate');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.failed',
        ]);
    }

    public function test_a_logout_is_audited(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'auth.logout',
        ]);
    }
}
```

- [ ] **Step 8: Run test to verify it fails**

Run: `php artisan test --filter=AuthenticationAuditTest`
Expected: FAIL — no `audit_logs` rows created yet on auth events.

- [ ] **Step 9: Create the listener**

Create `app/Support/Audit/Listeners/LogAuthenticationActivity.php`:

```php
<?php

namespace App\Support\Audit\Listeners;

use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    public function __construct(private AuditLogger $logger) {}

    public function handleLogin(Login $event): void
    {
        $this->logger->log('auth.login', $event->user);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user instanceof User) {
            $this->logger->log('auth.logout', $event->user, userId: $event->user->id, agencyId: $event->user->agency_id);
        }
    }

    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;
        $user = $email ? User::withoutGlobalScopes()->where('email', $email)->first() : null;

        $this->logger->log('auth.failed', agencyId: $user?->agency_id, userId: $user?->id);
    }
}
```

- [ ] **Step 10: Register the listener**

Modify `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Support\Audit\Listeners\LogAuthenticationActivity;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, [LogAuthenticationActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthenticationActivity::class, 'handleFailed']);
    }
}
```

- [ ] **Step 11: Fire the Login/Logout events explicitly**

`Auth::attempt()` (used in `app/Livewire/Auth/Login.php`) already fires `Illuminate\Auth\Events\Attempting`, `Failed` and `Login` internally — no change needed there. Fortify's default `POST /logout` route dispatches `Illuminate\Auth\Events\Logout` via `Illuminate\Auth\AuthManager::logout()` — also no change needed.

- [ ] **Step 12: Run test to verify it passes**

Run: `php artisan test --filter=AuthenticationAuditTest`
Expected: PASS (all 3 tests)

- [ ] **Step 13: Commit**

```bash
git add database/migrations/2026_07_26_000003_create_audit_logs_table.php app/Support/Audit app/Providers/AppServiceProvider.php tests/Feature/Support/Audit
git commit -m "Add audit logging for authentication events"
```

---

## Task 11: Admin — user list, invite, role assignment

**Files:**
- Create: `app/Livewire/Admin/UserIndex.php`, `resources/views/livewire/admin/user-index.blade.php`
- Create: `app/Livewire/Admin/UserForm.php`, `resources/views/livewire/admin/user-form.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Admin/UserManagementTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Admin/UserManagementTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Admin\UserForm;
use App\Livewire\Admin\UserIndex;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_with_permission_sees_only_their_agencys_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $agencyA = Agency::factory()->create();
        $agencyB = Agency::factory()->create();

        $admin = User::factory()->for($agencyA, 'agency')->create();
        $admin->assignRole('Administrateur');

        $colleague = User::factory()->for($agencyA, 'agency')->create();
        $otherAgencyUser = User::factory()->for($agencyB, 'agency')->create();

        $this->actingAs($admin);

        Livewire::test(UserIndex::class)
            ->assertSee($admin->name)
            ->assertSee($colleague->name)
            ->assertDontSee($otherAgencyUser->name);
    }

    public function test_a_user_without_permission_cannot_view_the_users_list_route(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();
        $user->assignRole('Agent');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_an_admin_can_invite_a_new_user_with_a_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $agency = Agency::factory()->create();
        $admin = User::factory()->for($agency, 'agency')->create();
        $admin->assignRole('Administrateur');

        $this->actingAs($admin);

        Livewire::test(UserForm::class)
            ->set('name', 'Nouvel Agent')
            ->set('email', 'agent@plateau.example')
            ->set('role', 'Agent')
            ->call('save');

        $newUser = User::where('email', 'agent@plateau.example')->first();
        $this->assertNotNull($newUser);
        $this->assertSame($agency->id, $newUser->agency_id);
        $this->assertTrue($newUser->hasRole('Agent'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=UserManagementTest`
Expected: FAIL — `Class "App\Livewire\Admin\UserIndex" not found`.

- [ ] **Step 3: Add the permission-gated routes**

Modify `routes/web.php`:

```php
<?php

use App\Livewire\Admin\UserForm;
use App\Livewire\Admin\UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => 'dashboard placeholder')->name('dashboard');

    Route::middleware('permission:users.view')->prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', UserIndex::class)->name('index');
    });

    Route::middleware('permission:users.create')->group(function () {
        Route::get('/admin/users/create', UserForm::class)->name('admin.users.create');
    });
});
```

- [ ] **Step 4: Create the UserIndex component**

Create `app/Livewire/Admin/UserIndex.php`:

```php
<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.user-index', [
            'users' => User::with('roles')->orderBy('name')->paginate(20),
        ]);
    }
}
```

- [ ] **Step 5: Create the UserIndex view**

Create `resources/views/livewire/admin/user-index.blade.php`:

```blade
<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Utilisateurs</h1>
        @can('users.create')
            <a href="{{ route('admin.users.create') }}"><x-button>Inviter un utilisateur</x-button></a>
        @endcan
    </div>

    <x-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400">
                    <th class="pb-2">Nom</th>
                    <th class="pb-2">Email</th>
                    <th class="pb-2">Rôle</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t border-gray-100 dark:border-gray-700">
                        <td class="py-2 text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                        <td class="py-2 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="py-2">
                            @foreach ($user->roles as $role)
                                <x-badge color="green">{{ $role->name }}</x-badge>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </x-card>
</div>
```

- [ ] **Step 6: Create the UserForm component**

Create `app/Livewire/Admin/UserForm.php`:

```php
<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('required|exists:roles,name')]
    public string $role = '';

    public function save()
    {
        $this->validate();

        $user = User::create([
            'agency_id' => Auth::user()->agency_id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make(Str::password(16)),
        ]);

        $user->assignRole($this->role);

        return redirect()->route('admin.users.index');
    }

    public function render()
    {
        return view('livewire.admin.user-form', [
            'roles' => Role::pluck('name'),
        ]);
    }
}
```

Password reset for invited users (a "set your password" email flow) is out of scope for this lot — the invited user's password is random and unknown; they use "Mot de passe oublié" on the login page to set their own. This is a deliberate scope cut, not an oversight: building a dedicated invitation-email flow belongs with the Notifications module (Phase 5), which does not exist yet.

- [ ] **Step 7: Create the UserForm view**

Create `resources/views/livewire/admin/user-form.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Inviter un utilisateur</h1>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-label for="name">Nom</x-label>
                <x-input wire:model="name" id="name" autofocus />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="email">Email</x-label>
                <x-input wire:model="email" type="email" id="email" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="role">Rôle</x-label>
                <select wire:model="role" id="role" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 text-sm">
                    <option value="">— Choisir —</option>
                    @foreach ($roles as $roleName)
                        <option value="{{ $roleName }}">{{ $roleName }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <x-button>Inviter</x-button>
        </form>
    </x-card>
</div>
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=UserManagementTest`
Expected: PASS (all 3 tests)

- [ ] **Step 9: Commit**

```bash
git add app/Livewire/Admin resources/views/livewire/admin routes/web.php tests/Feature/Admin
git commit -m "Add admin user list, invitation and role assignment"
```

---

## Task 12: Dashboard (empty state)

**Files:**
- Create: `app/Livewire/Dashboard.php`, `resources/views/livewire/dashboard.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/DashboardTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/DashboardTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_all_stat_cards_at_zero(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Biens')
            ->assertSee('Contrats actifs')
            ->assertSee('Loyers attendus')
            ->assertSee('Impayés');
    }

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=DashboardTest`
Expected: FAIL — the placeholder route from Task 8 returns a plain string, not the expected markup.

- [ ] **Step 3: Create the Dashboard component**

Create `app/Livewire/Dashboard.php`:

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $cards = [
            ['label' => 'Biens', 'value' => 0],
            ['label' => 'Biens occupés', 'value' => 0],
            ['label' => 'Biens vacants', 'value' => 0],
            ['label' => 'Contrats actifs', 'value' => 0],
            ['label' => 'Loyers attendus', 'value' => '0 FCFA'],
            ['label' => 'Loyers encaissés', 'value' => '0 FCFA'],
            ['label' => 'Impayés', 'value' => 0],
            ['label' => 'Montant des impayés', 'value' => '0 FCFA'],
            ['label' => 'Contrats expirant bientôt', 'value' => 0],
            ['label' => 'Échéances à venir', 'value' => 0],
        ];

        return view('livewire.dashboard', ['cards' => $cards]);
    }
}
```

- [ ] **Step 4: Create the Dashboard view**

Create `resources/views/livewire/dashboard.blade.php`:

```blade
<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tableau de bord</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($cards as $card)
            <x-card>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $card['value'] }}</p>
            </x-card>
        @endforeach
    </div>
</div>
```

- [ ] **Step 5: Wire the dashboard into the app layout and route**

Modify `routes/web.php` — replace the placeholder dashboard route:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // ... admin routes unchanged
});
```

Create no separate blade wrapper file — Livewire full-page components render inside the layout configured via `#[Layout]`. Add the attribute to the component:

Modify `app/Livewire/Dashboard.php` to use the app layout:

```php
<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $cards = [
            ['label' => 'Biens', 'value' => 0],
            ['label' => 'Biens occupés', 'value' => 0],
            ['label' => 'Biens vacants', 'value' => 0],
            ['label' => 'Contrats actifs', 'value' => 0],
            ['label' => 'Loyers attendus', 'value' => '0 FCFA'],
            ['label' => 'Loyers encaissés', 'value' => '0 FCFA'],
            ['label' => 'Impayés', 'value' => 0],
            ['label' => 'Montant des impayés', 'value' => '0 FCFA'],
            ['label' => 'Contrats expirant bientôt', 'value' => 0],
            ['label' => 'Échéances à venir', 'value' => 0],
        ];

        return view('livewire.dashboard', ['cards' => $cards]);
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=DashboardTest`
Expected: PASS (both tests)

- [ ] **Step 7: Commit**

```bash
git add app/Livewire/Dashboard.php resources/views/livewire/dashboard.blade.php routes/web.php tests/Feature/DashboardTest.php
git commit -m "Add empty-state dashboard"
```

---

## Task 13: "Coming soon" placeholder for unbuilt modules

**Files:**
- Create: `resources/views/coming-soon.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Ui/SidebarNavigationTest.php`

`App\Support\Navigation\SidebarMenu` (Task 7) already lists every module, including the ones without a route yet — the `Route::has()` guard in `app.blade.php` (also Task 7) renders those as `#` until this task adds the route they point to.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Ui/SidebarNavigationTest.php`:

```php
<?php

namespace Tests\Feature\Ui;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_lists_every_module_in_the_sidebar(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Biens')
            ->assertSee('Propriétaires')
            ->assertSee('Locataires')
            ->assertSee('Contrats')
            ->assertSee('Loyers')
            ->assertSee('Cautions')
            ->assertSee('Impayés')
            ->assertSee('Notifications')
            ->assertSee('Rapports')
            ->assertSee('Administration');
    }

    public function test_an_unbuilt_module_shows_the_coming_soon_page(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->for($agency, 'agency')->create();

        $this->actingAs($user)
            ->get(route('modules.coming-soon', ['module' => 'biens']))
            ->assertOk()
            ->assertSee('Bientôt disponible');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SidebarNavigationTest`
Expected: first assertion (dashboard lists every module) already PASSes — `SidebarMenu` and the dashboard exist since Tasks 7/12. The second test FAILs: `route('modules.coming-soon', ...)` throws `RouteNotFoundException` since that route isn't registered yet.

- [ ] **Step 3: Create the coming-soon view**

Create `resources/views/coming-soon.blade.php`:

```blade
<x-layouts.app title="Bientôt disponible — EasyImmob">
    <x-card>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Bientôt disponible</h1>
        <p class="text-gray-600 dark:text-gray-300">
            Le module « {{ ucfirst($module) }} » arrive dans une prochaine phase de développement.
        </p>
    </x-card>
</x-layouts.app>
```

- [ ] **Step 4: Add the route**

Modify `routes/web.php` — inside the `auth` middleware group:

```php
    Route::get('/modules/{module}', fn (string $module) => view('coming-soon', ['module' => $module]))
        ->name('modules.coming-soon');
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=SidebarNavigationTest`
Expected: PASS (both tests)

- [ ] **Step 6: Run the full suite to catch regressions**

Run: `php artisan test`
Expected: PASS — all tests from Tasks 1–13 still pass.

- [ ] **Step 7: Commit**

```bash
git add resources/views/coming-soon.blade.php routes/web.php tests/Feature/Ui/SidebarNavigationTest.php
git commit -m "Wire the coming-soon placeholder for unbuilt sidebar modules"
```

---

## Task 14: Final verification

**Files:** none (verification only)

- [ ] **Step 1: Run the full test suite**

Run: `php artisan test`
Expected: all tests pass (Tasks 2–13).

- [ ] **Step 2: Run migrations against the local MySQL database**

Run: `php artisan migrate:fresh --seed`
Expected: all migrations run in order (agencies → users+agency_id → permission tables → audit_logs, interleaved with whichever order Laravel discovers them in — all are independent except the two agency-dependent ones, which are correctly ordered by their `2026_07_26_00000X` timestamps), followed by `RolesAndPermissionsSeeder` output with no errors.

- [ ] **Step 3: Verify the route list**

Run: `php artisan route:list`
Expected: `login`, `register`, `password.request`, `password.reset`, `logout`, `dashboard`, `admin.users.index`, `admin.users.create`, `modules.coming-soon` all present with the expected HTTP verbs and middleware.

- [ ] **Step 4: Run the optimizer**

Run: `php artisan optimize`
Expected: completes without error (config, routes, views all cache cleanly — this would fail if any Blade view or config file has a syntax error).

- [ ] **Step 5: Manual smoke test**

Run: `php artisan serve` (or the existing `composer dev` script), then in a browser:
1. Visit `/register`, create an agency — confirm redirect to `/dashboard` with the sidebar, emerald theme, and all stat cards at zero.
2. Toggle dark mode — confirm the whole layout switches.
3. Visit `/admin/users`, confirm the invite form works and the new user appears with their role badge.
4. Log out, confirm redirect to `/login`.
5. Click "Mot de passe oublié", submit an email, confirm the generic confirmation message.
6. Click any sidebar module other than Dashboard/Administration — confirm the "Bientôt disponible" page renders inside the same layout.

No commit for this task — it is a verification pass, not a code change. If any step fails, fix the underlying issue and re-run the affected task's tests before returning here.
