<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codigo extends Model
{
    use HasFactory;

    protected $table = "Codigo_Verificacion";
    
    protected $fillable = [
        'codigo_web',
        'codigo_Verificacion_web',
        'codigo_movil',
        'codigo_Verificacion_movil',

    ];

}