<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioMercadoLibre extends Model
{
    use HasFactory;

    protected $table = 'usuarios_mercado_libre';

    protected $fillable = [
        'user_id',
        'nickname',
        'password',
        'site_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
