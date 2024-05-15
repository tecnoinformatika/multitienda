<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
