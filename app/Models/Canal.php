<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    protected $table = 'canales';
    use HasFactory;
    protected $fillable = [
        'canal',
        'nombre',
        'url',
        'secret',
        'apikey',
        'pais',
        'incrementoprecio',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
