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
        'productosWoo',
        'productosShopify',
        'productosMely',
        'productosMelyShops',
        'productosPrestashop',
        'productosLinio',
        'productosFalabella',
        'productosFacebook',
        'totalproductos',
        'user_id',
        'token',
        'refresh_token',
        'token_type',
        'expires_in',
        'scope',
        'MeliUser_id'
    ];

    protected $casts = [
        'productosWoo' => 'json',
        'productosShopify' => 'json',
        'productosMely' => 'json',
        'productosMelyShops' => 'json',
        'productosPrestashop' => 'json',
        'productosLinio' => 'json',
        'productosFalabella' => 'json',
        'productosFacebook' => 'json',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

}
