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
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layer_id')->constrained()->onDelete('cascade');
            $table->string('land_use')->nullable();
            $table->string('ownership')->nullable();
            $table->string('classification')->nullable();
            $table->enum('flood_risk', ['none', 'low', 'medium', 'high'])->default('none');
            $table->enum('health_status', ['poor', 'fair', 'good', 'excellent'])->default('fair');
            $table->integer('disease_cases')->default(0);
            $table->integer('clinics_available')->default(0);
            $table->text('additional_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata');
    }
};
