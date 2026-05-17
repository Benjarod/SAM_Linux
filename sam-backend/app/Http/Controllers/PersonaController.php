<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class PersonaController extends Controller
{
    public function guardarVarias(Request $request)
    {
        $user = $request->user(); // Usuario autenticado
        $personas = $request->all(); // Recibe directamente el array

        if (!is_array($personas)) {
            return response()->json(['message' => 'Formato incorrecto. Se esperaba un array.'], 400);
        }

        foreach ($personas as $persona) {
            Person::create([
                'rut'              => $persona['rut'] ?? null,
                'nombres'          => $persona['nombres'] ?? null,
                'ap_pat'           => $persona['ap_pat'] ?? null,
                'ap_mat'           => $persona['ap_mat'] ?? null,
                'fecha_nacimiento' => $persona['fecha_nacimiento'] ?? null,
                'sexo'             => $persona['sexo'] ?? null,
                'ficha_clinica'    => $persona['ficha_clinica'] ?? null,
                'domicilio'        => $persona['domicilio'] ?? null,
                'fono'             => $persona['fono'] ?? null,
                'celu'             => $persona['celu'] ?? null,
                'email'            => $persona['email'] ?? null,
                'user_id'          => $user->id,
            ]);
        }

        return response()->json(['message' => 'Personas guardadas correctamente.']);
    }

    public function guardarSeleccionados(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $user = $request->user();

        // Guardamos los IDs seleccionados temporalmente
        cache()->put("seleccionados_{$user->email}", $request->ids, now()->addHours(1));

        return response()->json(['message' => 'Selección guardada correctamente.']);
    }

    public function obtenerSeleccionados(Request $request)
    {
        $user = $request->user();
        $seleccionados = cache()->get("seleccionados_{$user->email}", []);
        return response()->json($seleccionados);
    }

    public function obtenerPersonas(Request $request)
    {
        return Person::where('user_id', $request->user()->id)->get();
    }

    public function eliminarTodas(Request $request)
    {
        Person::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Registros del usuario eliminados.']);
    }
}
