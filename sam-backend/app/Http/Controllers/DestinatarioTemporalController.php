<?php

namespace App\Http\Controllers;

use App\Models\DestinatarioTemporal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DestinatarioTemporalController extends Controller
{
    public function store(Request $request)
    {
        $usuario_email = Auth::user()->email;

        // Eliminar registros anteriores de ese usuario
        DestinatarioTemporal::where('usuario_email', $usuario_email)->delete();

        $personas = $request->input('destinatarios');

        foreach ($personas as $persona) {
            DestinatarioTemporal::create([
                'rut' => $persona['rut'] ?? null,
                'nombres' => $persona['nombres'] ?? null,
                'ap_pat' => $persona['ap_pat'] ?? null,
                'ap_mat' => $persona['ap_mat'] ?? null,
                'sexo' => $persona['sexo'] ?? null,
                'ficha_clinica' => $persona['ficha_clinica'] ?? null,
                'domicilio' => $persona['domicilio'] ?? null,
                'fono' => $persona['fono'] ?? null,
                'celu' => $persona['celu'] ?? null,
                'email' => $persona['email'] ?? null,
                'usuario_email' => $usuario_email,
            ]);
        }

        return response()->json(['message' => 'Destinatarios guardados correctamente']);
    }

    // Verificar si existen destinatarios guardados por usuario
    public function verificar(Request $request)
    {
    $usuario_email = $request->user()->email;

    $existe = \App\Models\DestinatarioTemporal::where('usuario_email', $usuario_email)->exists();

    return response()->json([
        'existen' => $existe
    ]);
    }
    
    public function eliminarPorUsuario(Request $request)
    {
        $request->validate([
            'usuario_email' => 'required|email'
        ]);

        $deleted = \DB::table('destinatarios_temporales')
            ->where('usuario_email', $request->usuario_email)
            ->delete();

        return response()->json([
            'message' => 'Destinatarios temporales eliminados correctamente.',
            'deleted_count' => $deleted
        ]);
    }

    
}
