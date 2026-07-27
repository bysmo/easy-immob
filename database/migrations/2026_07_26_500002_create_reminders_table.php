<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->restrictOnDelete();
            $table->foreignId('arrears_id')->constrained('arrears')->cascadeOnDelete();
            $table->string('channel')->default('email');
            $table->timestamp('sent_at');
            $table->text('content');
            $table->string('status')->default('sent');
            $table->timestamps();

            $table->index(['agency_id', 'arrears_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
