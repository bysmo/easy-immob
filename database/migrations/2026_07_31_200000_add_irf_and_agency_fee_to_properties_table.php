<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_subject_to_irf')->default(false)->after('rent_amount');
            $table->string('agency_fee_type')->default('percentage')->after('is_subject_to_irf');
            $table->decimal('agency_fee_value', 10, 2)->nullable()->after('agency_fee_type');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['is_subject_to_irf', 'agency_fee_type', 'agency_fee_value']);
        });
    }
};
