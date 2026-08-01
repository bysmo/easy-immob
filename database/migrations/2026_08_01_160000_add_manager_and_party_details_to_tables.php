<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('manager_name')->nullable()->after('legal_name')->comment('Nom complet du gérant/responsable');
            $table->string('manager_title')->nullable()->after('manager_name')->comment('Titre/Qualité du responsable (Gérant, etc.)');
            $table->string('manager_phone')->nullable()->after('manager_title')->comment('Téléphone direct du responsable');
            $table->string('manager_id_card')->nullable()->after('manager_phone')->comment('Pièce d\'identité du responsable');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('address');
            $table->string('nationality')->nullable()->default('Burkinabè')->after('profession');
            $table->string('id_card_number')->nullable()->after('nationality')->comment('Numéro et détails de la pièce d\'identité');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('address');
            $table->string('nationality')->nullable()->default('Burkinabè')->after('profession');
            $table->string('id_card_number')->nullable()->after('nationality')->comment('Numéro et détails de la pièce d\'identité');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['manager_name', 'manager_title', 'manager_phone', 'manager_id_card']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['profession', 'nationality', 'id_card_number']);
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['profession', 'nationality', 'id_card_number']);
        });
    }
};
