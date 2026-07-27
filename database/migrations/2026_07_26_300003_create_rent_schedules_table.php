<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->string('period', 7)->comment('AAAAMM ex: 2026-08');
            $table->date('due_date');
            $table->decimal('expected_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['agency_id', 'lease_id', 'period']);
            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_schedules');
    }
};
