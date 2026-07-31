<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->string('reference');
            $table->unique(['agency_id', 'reference']);
            $table->foreignId('property_id')->constrained('properties')->restrictOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('lease_templates')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('charges_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('payment_due_day')->default(5);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamps();

            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'property_id']);
            $table->index(['agency_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
