<?php

namespace App\Http\Controllers;

use App\Mail\PromocionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CorreoController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'plantilla_id' => 'required|integer',
            'nombre_remitente' => 'required|string',
            'asunto' => 'required|string',
            'usuario_email' => 'required|email',
            // Ya no necesitamos validar 'remitente' aquí
        ]);

        // ✅ Log del payload recibido
        Log::info('📥 Payload recibido para envío de correo:', $request->all());

        $user = User::where('email', $request->usuario_email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        $plantilla = Promotion::where('id', $request->plantilla_id)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$plantilla) {
            return response()->json(['message' => 'Plantilla no encontrada o no pertenece al usuario.'], 404);
        }

        // ✅ Solo destinatarios con email válido
        $destinatarios = DB::table('destinatarios_temporales')
            ->where('usuario_email', $request->usuario_email)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($destinatarios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron destinatarios con email válido.'], 404);
        }

        // ✅ SMTP personalizado
        $smtp = $user->smtpSetting;

        if (!$smtp) {
            return response()->json(['message' => 'No hay configuración SMTP registrada para este usuario.'], 422);
        }

        $esGmail = str_contains($smtp->host, 'gmail');
        $email_from = $esGmail ? $smtp->username : ($smtp->email_from ?? $smtp->username);

        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $smtp->host,
            'port' => $smtp->port,
            'encryption' => $smtp->encryption,
            'username' => $smtp->username,
            'password' => $smtp->password,
        ]);

        Config::set('mail.from.address', $email_from);
        Config::set('mail.from.name', $request->nombre_remitente);
        Config::set('mail.default', 'smtp');

        // ✅ Envío por cola (queue)
        foreach ($destinatarios as $destinatario) {
            Log::info('📧 Encolando correo a: ' . $destinatario->email);
            Log::info('🧍 Nombre del destinatario: ' . json_encode($destinatario->nombres));

            $smtpConfig = [
                'host' => $smtp->host,
                'port' => $smtp->port,
                'encryption' => $smtp->encryption,
                'username' => $smtp->username,
                'password' => $smtp->password,
                'from' => $email_from,
                'name' => $request->nombre_remitente,
            ];

            Mail::mailer('smtp')->to($destinatario->email)
                ->send(new PromocionMail(
                    ['nombres' => $destinatario->nombres ?? 'Usuario'],
                    $plantilla->description,
                    $request->asunto,
                    $smtpConfig
                ));
        }

        return response()->json(['message' => 'Correos enviados a la cola exitosamente.']);
    }

    public function contarDestinatarios($email)
    {
        $cantidad = DB::table('destinatarios_temporales')
                      ->where('usuario_email', $email)
                      ->whereNotNull('email')
                      ->where('email', '!=', '')
                      ->count();

        return response()->json($cantidad);
    }
}
