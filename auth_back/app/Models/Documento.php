<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    // ASEGÚRATE DE QUE $fillable SE VEA ASÍ
    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'numero_folios',
        'pdf_url',
    ];
}
