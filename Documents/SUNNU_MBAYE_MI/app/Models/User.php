<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Annonce;
use App\Models\Produit;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public function formation()
    {
        return $this->hasMany(Commende::class);
    }

    public function produit()
    {
        return $this->hasMany(Produit::class);
    }

    public function annonce()
    {
        return $this->hasMany(Annonce::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // ... autres parties de votre modèle ...

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'prenom',
        'contact',
        'sexe',
        'profile',
        'date_naissance',
        'adresse',
        'password',
        'role_id',
    ];

}

