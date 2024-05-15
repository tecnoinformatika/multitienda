<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'payment_date',
        'total_paid_amount',
        'currency',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
