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
            $table->unsignedBigInteger('commende_id');
            $table->foreign('commende_id')->references('id')->on('commendes')->onDelete('cascade');
            $table->unsignedBigInteger('produit_id');
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
             $table->integer('nombre_produit');
            $table->integer('montant'); 
            $table->string('nom_produit')->nulabla(); 
            
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('detail_commendes');
    }
    
    
};
