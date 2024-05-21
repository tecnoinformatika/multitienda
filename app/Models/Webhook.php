<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'canal_id',
        'status',
        'topic',
        'resource',
        'event',
        'hooks',
        'delivery_url',
        'date_created',
        'date_created_gmt',
        'date_modified',
        'date_modified_gmt',
    ];

    protected $casts = [
        'hooks' => 'array',
        'date_created' => 'datetime',
        'date_created_gmt' => 'datetime',
        'date_modified' => 'datetime',
        'date_modified_gmt' => 'datetime',
    ];
}
