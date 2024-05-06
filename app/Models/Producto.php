<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id','modelo','nombre',  'total_existencia', 'titulo', 'marca_id','marca_logo',
        'sat_key', 'img_portada', 'categorías', 'link', 'precios', 'existencia','imagenes','marca',
        'caracteristicas', 'descripcion', 'recursos','pvol','unidad_de_medida','alto','largo','ancho','categorias','nivel1','nivel2','nivel3'
    ];

    protected $casts = [
        'categorias' => 'json',
        'precios' => 'json',
        'existencia' => 'json',
        'caracteristicas' => 'json',
        'recursos' => 'json',
        'unidad_de_medida' => 'json',
        'imagenes' => 'json',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
