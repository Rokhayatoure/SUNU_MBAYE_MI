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
        Schema::create('commendes', function (Blueprint $table) {
            $table->id();
            $table->enum('livraison', ['En_court', 'annuler','terminer',])->default('En_court');
            $table->integer("numero_commende")->nullable();
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commendes');
    }
};
