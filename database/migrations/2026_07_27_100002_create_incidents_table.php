<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lease_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('reference');
            $table->unique(['agency_id', 'reference']);
            $table->string('title');
            $table->text('description');
            $table->string('audio_path')->nullable()->comment('Enregistrement audio vocal du locataire');
            $table->json('photos')->nullable()->comment('Photos envoyées par le locataire');
            $table->json('videos')->nullable()->comment('Vidéos envoyées par le locataire');
            $table->string('status')->default('reported'); // reported, in_progress, resolved, closed, rejected
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->text('repair_details')->nullable()->comment('Description de la réparation effectuée');
            $table->decimal('repair_cost', 12, 2)->default(0)->comment('Coût de réparation - visible agence uniquement');
            $table->string('tenant_confirmation_photo')->nullable()->comment('Photo de confirmation du locataire après réparation');
            $table->text('tenant_confirmation_note')->nullable()->comment('Remarque de confirmation du locataire');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agency_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
