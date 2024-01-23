<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'id',
        'nom_produit',
        'quantiter',
        'images',
        'users_id',
        'prix',
        'categorie_id'
    ];
    public function users(): BelongsTo
    {
        return $this-> BelongsTo(User::class,);
    }
    public function Categori(): BelongsTo
    {
        return $this-> BelongsTo(Categorie::class, );
    }
    public function detailCommende()
    {
        return $this->hasMany(Detail_Commende::class);
    }

}
