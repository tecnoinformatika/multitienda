<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'nombre', 'logo', 'descripcion'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
