<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminoAtributo extends Model
{
    use HasFactory;
    protected $table = 'terminos_atributos';
    protected $fillable = ['nombre','slug', 'atributo_id'];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class);
    }
}
