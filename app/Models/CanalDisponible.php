<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanalDisponible extends Model
{
    protected $table = 'canal_disponible';
    use HasFactory;
    protected $fillable = [
        'nombre',
        'tipo_canal',
        'url',
        'svg',
    ];
    
}
