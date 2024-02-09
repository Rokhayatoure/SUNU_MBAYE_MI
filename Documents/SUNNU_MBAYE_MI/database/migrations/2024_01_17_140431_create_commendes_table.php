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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->unsignedBigInteger('produit_id');
            // $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
            $table->enum('livraison', ['En_court', 'annuler','terminer',])->default('En_court');
            $table->integer("numero_commende")->nullable();
            $table->string('prenom')->nullable();
            $table->string('nom')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->integer('prix')->nullable();
            $table->string('images')->nullable();
            $table->integer('quantite')->nullable();
            $table->string('nom_prouit');
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
