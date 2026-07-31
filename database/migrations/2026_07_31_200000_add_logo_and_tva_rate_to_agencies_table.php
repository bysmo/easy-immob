<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('address');
            $table->decimal('tva_rate', 5, 2)->default(18.00)->nullable()->after('is_subject_to_tva');
            $table->string('nif_rccm')->nullable()->after('tva_rate');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'tva_rate', 'nif_rccm']);
        });
    }
};
