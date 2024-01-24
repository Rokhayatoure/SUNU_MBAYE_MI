<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class panier extends Model
{
    use HasFactory;
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
