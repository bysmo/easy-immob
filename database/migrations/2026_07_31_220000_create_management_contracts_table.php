<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $table->string('reference');
            $table->string('title')->default('Mandat de Gestion Immobilière');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_months')->default(12);
            $table->string('commission_type')->default('percentage'); // percentage, fixed
            $table->decimal('commission_value', 10, 2)->default(10.00); // e.g. 10% or 15000 FCFA
            $table->decimal('agreed_rent_amount', 12, 2)->nullable(); // Loyer prévisionnel
            $table->boolean('irf_paid_by_owner')->default(true);
            $table->boolean('caution_kept_by_agency')->default(true);
            $table->integer('notice_period_months')->default(3);
            $table->text('payment_bank_details')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('status')->default('active'); // draft, active, expired, terminated
            $table->timestamp('signed_at')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['agency_id', 'reference']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('management_contract_id')
                ->nullable()
                ->after('owner_id')
                ->constrained('management_contracts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['management_contract_id']);
            $table->dropColumn('management_contract_id');
        });

        Schema::dropIfExists('management_contracts');
    }
};
