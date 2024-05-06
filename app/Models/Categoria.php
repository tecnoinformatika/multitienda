<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'categoria_id','nombre', 'nivel', 'origen', 'subcategorías'
    ];

    protected $casts = [
        'origen' => 'array',
        'subcategorías' => 'array'
    ];

    
}
