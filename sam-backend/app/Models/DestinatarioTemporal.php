<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinatarioTemporal extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'nombres',
        'ap_pat',
        'ap_mat',
        'sexo',
        'ficha_clinica',
        'domicilio',
        'fono',
        'celu',
        'email',
        'usuario_email',
    ];

    protected $table = 'destinatarios_temporales';
}
