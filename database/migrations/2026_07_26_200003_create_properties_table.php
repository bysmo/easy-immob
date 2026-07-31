<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->string('reference');
            $table->unique(['agency_id', 'reference']);
            $table->foreignId('owner_id')->constrained('owners')->restrictOnDelete();
            $table->foreignId('property_type_id')->constrained('property_types')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('neighborhood')->nullable();
            $table->decimal('surface_area', 10, 2)->nullable()->comment('Surface en m²');
            $table->unsignedSmallInteger('bedrooms')->nullable();
            $table->unsignedSmallInteger('bathrooms')->nullable();
            $table->decimal('rent_amount', 12, 2)->default(0)->comment('Loyer mensuel demandé');
            $table->string('status')->default('available');
            $table->timestamps();

            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
