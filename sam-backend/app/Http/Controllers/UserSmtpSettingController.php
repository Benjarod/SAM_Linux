<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSmtpSetting;
use Illuminate\Support\Facades\Auth;

class UserSmtpSettingController extends Controller
{
    public function store(Request $request)
    {
        // Validaciones base
        $validationRules = [
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
        ];

        // Si el host es Gmail, no requerimos email_from
        if (str_contains($request->host, 'gmail')) {
            $validationRules['email_from'] = 'nullable|email';
            // Si está vacío, usamos el username como email_from
            if (empty($request->email_from)) {
                $request->merge(['email_from' => $request->username]);
            }
        } else {
            $validationRules['email_from'] = 'required|email';
        }

        $request->validate($validationRules);

        $user = Auth::user();

        $data = [
            'user_id'    => $user->id,
            'mailer'     => $request->input('mailer'),
            'host'       => $request->input('host'),
            'port'       => $request->input('port'),
            'username'   => $request->input('username'),
            'password'   => $request->input('password'),
            'encryption' => $request->input('encryption'),
            'email_from' => $request->input('email_from'),
        ];

        UserSmtpSetting::updateOrCreate(['user_id' => $user->id], $data);

        return response()->json(['message' => 'Configuración SMTP guardada exitosamente.']);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $config = $user->smtpSetting; // Corrección aquí (singular)

        if (!$config) {
            return response()->json(null, 204); // Sin contenido
        }

        return response()->json($config);
    }

}
