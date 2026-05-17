<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinatarioSeleccionado extends Model
{
    use HasFactory;

    protected $table = 'destinatarios_temporales'; // 👈 AQUI está el fix

    protected $fillable = [
        'usuario_email',
        'rut',
        'email',
    ];
}
