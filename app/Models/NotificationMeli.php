<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMeli extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'resource',
        'user_id',
        'topic',
        'application_id',
        'attempts',
        'sent',
        'received',
    ];
}
