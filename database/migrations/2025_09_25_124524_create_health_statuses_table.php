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
        Schema::create('health_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor'])->default('fair');
            $table->integer('disease_cases')->default(0);
            $table->integer('clinics_available')->default(0);
            $table->string('land_use')->nullable();
            $table->json('geometry')->nullable(); // GeoJSON or polygon data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_statuses');
    }
};
