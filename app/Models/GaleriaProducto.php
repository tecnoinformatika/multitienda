<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriaProducto extends Model
{
    use HasFactory;

    protected $fillable = ['imagen', 'producto_id'];

    /**
     * Obtiene el producto al que pertenece la imagen de la galería.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
