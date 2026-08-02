<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portail Bailleur :
 *  - owners.user_id  : lien vers le compte utilisateur du bailleur
 *  - incidents.owner_confirmed_at : date à laquelle le bailleur a approuvé le coût de réparation
 *  - incidents.owner_confirmed_amount : montant approuvé par le bailleur
 *  - incidents.owner_confirmation_note : commentaire du bailleur
 */
return new class extends Migration
{
    public function up(): void
    {
        // Lier un bailleur à un compte utilisateur
        Schema::table('owners', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('agency_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // Approbation du coût de réparation par le bailleur
        Schema::table('incidents', function (Blueprint $table) {
            $table->timestamp('owner_confirmed_at')->nullable()->after('closed_at');
            $table->decimal('owner_confirmed_amount', 12, 2)->nullable()->after('owner_confirmed_at');
            $table->text('owner_confirmation_note')->nullable()->after('owner_confirmed_amount');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['owner_confirmed_at', 'owner_confirmed_amount', 'owner_confirmation_note']);
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
