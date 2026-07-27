<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('rent_schedule_id')->constrained('rent_schedules')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->decimal('amount_due', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->date('first_overdue_date');
            $table->string('severity')->default('warning');
            $table->string('status')->default('open');
            $table->timestamps();

            $table->unique(['agency_id', 'rent_schedule_id']);
            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrears');
    }
};
