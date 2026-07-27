<?php

namespace Tests;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Seed roles & permissions after the database is refreshed.
     *
     * Tests that use RefreshDatabase should call this method (or use the
     * artisan seeder via $seeder property) whenever they need roles.
     * We expose it as a helper rather than running it globally so that
     * unit tests not using the DB don't pay the cost.
     */
    protected function seedRolesAndPermissions(): void
    {
        $this->artisan('db:seed', ['--class' => RolesAndPermissionsSeeder::class]);
        // Clear Spatie's in-memory permission cache after seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
