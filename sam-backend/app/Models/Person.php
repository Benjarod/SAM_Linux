<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Person extends Model
{
    use HasFactory;
    protected $fillable = ['rut','nombres','ap_pat','ap_mat','fecha_nacimiento', 'sexo', 'ficha_clinica', 'domicilio', 'fono', 'celu', 'email', 'user_id'];
    public $timestamps = false;
    public function getAgeAttribute()
    // funcion creada para poder mostrar la edad como numero en la tabla powergrid de la vista GestionBD
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
