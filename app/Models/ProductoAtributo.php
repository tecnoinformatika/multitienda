<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAtributo extends Model
{
    use HasFactory;
    protected $table = 'producto_atributo';

    protected $fillable = [
        'producto_id', 'atributo_id', 'termino_atributo_id'
    ];

    // Opcionalmente, puedes definir relaciones con los modelos de Producto, Atributo y TerminoAtributo
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class);
    }

    public function terminoAtributo()
    {
        return $this->belongsTo(TerminoAtributo::class);
    }
}
