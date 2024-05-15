<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_method',
        'shipping_status',
        'shipping_date',
        'tracking_number',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
