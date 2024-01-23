<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commende extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_commende',
        'livraison'
        
    ];
    public function detailCommende()
    {
        return $this->hasMany(Detail_Commende::class);
    }

}
