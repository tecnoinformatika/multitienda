<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','slug'];

    public function terminos()
    {
        return $this->belongsToMany(TerminoAtributo::class);
    }
    // En el modelo Atributo
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_atributo');
    }

}
