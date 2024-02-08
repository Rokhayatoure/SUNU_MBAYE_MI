<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\SwaggerExclude
 */

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        // 'user_id',
        'commende_id',
        'token',
        'amount',
        // 'qty'
    ];

    protected $table = 'payments';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Commende()
    {
        return $this->belongsTo(Commende::class);
    }

}