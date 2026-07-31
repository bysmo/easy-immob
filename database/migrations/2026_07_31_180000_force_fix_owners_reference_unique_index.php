<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['owners', 'properties', 'tenants', 'leases', 'payments', 'incidents'];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            // 1. Force drop single-column unique constraint on reference by index name
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropUnique("{$tableName}_reference_unique");
                });
            } catch (\Throwable $e) {
                // Already dropped or different index name
            }

            // 2. Force drop single-column unique constraint by column name
            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropUnique(['reference']);
                });
            } catch (\Throwable $e) {
                // Already dropped
            }

            // 3. Fallback for MySQL raw alter if index still exists
            try {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$tableName}_reference_unique`");
                }
            } catch (\Throwable $e) {
                // Index does not exist
            }

            // 4. Ensure composite unique constraint (agency_id, reference) exists
            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unique(['agency_id', 'reference']);
                });
            } catch (\Throwable $e) {
                // Already exists
            }
        }
    }

    public function down(): void
    {
        $tables = ['owners', 'properties', 'tenants', 'leases', 'payments', 'incidents'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropUnique(['agency_id', 'reference']);
                    });
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
        }
    }
};
