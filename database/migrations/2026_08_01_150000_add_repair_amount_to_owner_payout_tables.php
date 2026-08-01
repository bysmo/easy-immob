<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajout de repair_amount dans owner_payouts
        Schema::table('owner_payouts', function (Blueprint $table) {
            $table->decimal('repair_amount', 12, 2)->default(0)->after('irf_amount')
                ->comment('Somme des coûts de réparations défalqués du reversement bailleur');
        });

        // Ajout de repair_amount dans owner_payout_items
        Schema::table('owner_payout_items', function (Blueprint $table) {
            $table->decimal('repair_amount', 12, 2)->default(0)->after('irf_amount')
                ->comment('Coût total des réparations sur ce bien pour la période');
        });
    }

    public function down(): void
    {
        Schema::table('owner_payouts', function (Blueprint $table) {
            $table->dropColumn('repair_amount');
        });

        Schema::table('owner_payout_items', function (Blueprint $table) {
            $table->dropColumn('repair_amount');
        });
    }
};
