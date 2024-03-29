<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
