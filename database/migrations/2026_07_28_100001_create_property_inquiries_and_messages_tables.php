<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->string('status')->default('open'); // open, draft_lease_created, closed
            $table->timestamps();

            $table->index(['agency_id', 'property_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('property_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('property_inquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_agency')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_chat_messages');
        Schema::dropIfExists('property_inquiries');
    }
};
