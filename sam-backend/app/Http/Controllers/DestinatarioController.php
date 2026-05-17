<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestinatarioSeleccionado;

class DestinatarioController extends Controller
{
    public function guardarSeleccionados(Request $request)
    {
        $request->validate([
            'destinatarios' => 'required|array',
            'destinatarios.*.rut' => 'required|string',
            'destinatarios.*.email' => 'required|email',
        ]);

        $usuarioEmail = $request->user()->email;

        // Limpiar registros anteriores
        DestinatarioSeleccionado::where('usuario_email', $usuarioEmail)->delete();

        // Guardar los nuevos seleccionados
        foreach ($request->destinatarios as $dest) {
            DestinatarioSeleccionado::create([
                'usuario_email' => $usuarioEmail,
                'rut' => $dest['rut'],
                'email' => $dest['email'],
            ]);
        }

        return response()->json(['message' => 'Destinatarios guardados correctamente.']);
    }

    public function obtenerSeleccionados(Request $request)
    {
        $usuarioEmail = $request->user()->email;
        $destinatarios = DestinatarioSeleccionado::where('usuario_email', $usuarioEmail)->get();

        return response()->json($destinatarios);
    }

    public function contarSeleccionados($email)
    {
    $cantidad = \App\Models\DestinatarioSeleccionado::where('usuario_email', $email)->count();
    return response()->json($cantidad);
    }
}
