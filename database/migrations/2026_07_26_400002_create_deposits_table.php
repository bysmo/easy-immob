<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->decimal('expected_amount', 12, 2);
            $table->decimal('received_amount', 12, 2)->default(0);
            $table->date('received_at')->nullable();
            $table->decimal('retained_amount', 12, 2)->default(0);
            $table->text('retention_reason')->nullable();
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->date('refunded_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['agency_id', 'lease_id']);
            $table->index(['agency_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
