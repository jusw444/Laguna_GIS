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
        Schema::create('land_uses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Area Name
            $table->string('land_use')->nullable(); // Residential, Commercial, etc
            $table->string('ownership')->nullable(); // Private, Public, Government
            $table->string('classification')->nullable(); // Extra classification
            $table->string('flood_risk')->nullable(); // High, Medium, Low
            $table->json('geometry')->nullable(); // GeoJSON data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_uses');
    }
};
