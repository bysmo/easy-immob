<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('owner_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->string('reference');
            $table->string('period'); // e.g. "2026-07"
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('calculation_type')->default('collected'); // collected, expected
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->float('commission_rate')->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('irf_amount', 12, 2)->default(0);
            $table->decimal('other_deductions_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, partially_paid, paid, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agency_id', 'period']);
            $table->index(['agency_id', 'owner_id']);
            $table->index(['agency_id', 'status']);
        });

        Schema::create('owner_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_payout_id')->constrained('owner_payouts')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('rent_schedule_id')->nullable()->constrained('rent_schedules')->nullOnDelete();
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('irf_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('owner_payout_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_payout_id')->constrained('owner_payouts')->cascadeOnDelete();
            $table->string('reference');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // mobile_money, bank_transfer, bank_deposit, cash, check, etc.
            $table->string('proof_document_path')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['agency_id', 'owner_payout_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_payout_settlements');
        Schema::dropIfExists('owner_payout_items');
        Schema::dropIfExists('owner_payouts');
    }
};
