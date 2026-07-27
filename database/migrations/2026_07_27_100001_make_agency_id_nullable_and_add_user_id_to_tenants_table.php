<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->after('agency_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('agency_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable(false)->change();
        });
    }
};
