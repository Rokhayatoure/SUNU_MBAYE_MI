<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCommende extends Model
{
    use HasFactory;
    protected $fillable = [
        'commende_id',
        'produit_id',
           'nom',
           'prenom',
           'contact',
           'prix',
           'images',
           'quantite',
           'nom_produit',
           'email'

        
    ];
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function commende()
    {
        return $this->belongsTo(Commende::class, 'commende_id');
    }
}
