<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['owners', 'properties', 'tenants', 'leases', 'payments', 'incidents'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $oldIndex = "{$tableName}_reference_unique";
                $newIndex = "{$tableName}_agency_id_reference_unique";

                if (Schema::hasIndex($tableName, $oldIndex)) {
                    Schema::table($tableName, function (Blueprint $table) use ($oldIndex) {
                        $table->dropUnique($oldIndex);
                    });
                }

                if (! Schema::hasIndex($tableName, $newIndex)) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->unique(['agency_id', 'reference']);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        $tables = ['owners', 'properties', 'tenants', 'leases', 'payments', 'incidents'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $newIndex = "{$tableName}_agency_id_reference_unique";

                if (Schema::hasIndex($tableName, $newIndex)) {
                    Schema::table($tableName, function (Blueprint $table) use ($newIndex) {
                        $table->dropUnique($newIndex);
                    });
                }
            }
        }
    }
};
