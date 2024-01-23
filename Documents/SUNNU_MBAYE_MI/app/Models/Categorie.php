<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom_categories',
        
    ];
    public function produit(): HasMany
    {
        return $this-> HasMany(Produit::class, );
    }

}
