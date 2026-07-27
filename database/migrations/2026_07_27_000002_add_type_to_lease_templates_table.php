<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_templates', function (Blueprint $table) {
            $table->string('type')->default('lease')->after('name'); // lease, management
        });
    }

    public function down(): void
    {
        Schema::table('lease_templates', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
