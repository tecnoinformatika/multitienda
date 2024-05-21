<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'platform_order_id',
        'status',
        'customers_id',
        'canal_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    public function shipping()
    {
        return $this->hasOne(OrderShipping::class);
    }

    public function billing()
    {
        return $this->hasOne(OrderBilling::class);
    }
    public function canal()
    {
        return $this->belongsTo(Canal::class, 'canal_id');
    }
}
