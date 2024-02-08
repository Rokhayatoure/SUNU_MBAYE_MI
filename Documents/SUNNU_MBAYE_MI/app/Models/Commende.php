<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commende extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'produit_id',
        'nom',
        'prenom',
        'contact',
        'prix',
        'images',
        'quantite',
        'nom_produit',
        'email',
        'user_id'
    ];
    public function detailCommende()
    {
        return $this->hasMany(DetailCommende::class);
    }
    public function Payement()
    {
        return $this->belongsTo(Payment::class);
    }

}
