<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('address');
            $table->boolean('is_subject_to_tva')->default(true)->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'is_subject_to_tva']);
        });
    }
};
