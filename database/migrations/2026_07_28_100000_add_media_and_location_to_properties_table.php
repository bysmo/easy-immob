<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('neighborhood');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('google_maps_url')->nullable()->after('longitude');
            $table->json('photos')->nullable()->after('rent_amount');
            $table->json('videos')->nullable()->after('photos')->comment('Max 3 vidéos');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'google_maps_url', 'photos', 'videos']);
        });
    }
};
