<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nickname',
        'first_name',
        'last_name',
        'document',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postcode',
        'country',
        'canal_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function canal()
    {
        return $this->belongsTo(Canal::class);
    }
}
