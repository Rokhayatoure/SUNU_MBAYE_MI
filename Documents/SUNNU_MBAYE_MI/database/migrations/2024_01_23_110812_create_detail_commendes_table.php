<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('detail_commendes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('produit_id');
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
            $table->string('prenom');
            $table->string('nom');
            $table->string('contact');
            $table->string('email');
            $table->string('prix');
            $table->string('images');
            $table->string('quantite');
            $table->string('nom_prouit');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('detail_commendes');
    }
    
    
};
