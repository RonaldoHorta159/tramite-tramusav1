<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    // ASEGÚRATE DE QUE $fillable SE VEA ASÍ
    protected $fillable = [
        'documento_id',
        'origen_id',
        'destino_id',
        'asunto',
        'estado',
        'CU',
        'proveido',
    ];

    // ... (El resto del modelo no cambia) ...
}
