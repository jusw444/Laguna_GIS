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
        Schema::create('flood_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('flood_risk', ['high', 'medium', 'low', 'none']);
            $table->string('land_use')->nullable();
            $table->string('ownership')->nullable();
            $table->string('classification')->nullable();
            $table->json('geometry')->nullable(); // GeoJSON or polygon data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_areas');
    }
};
