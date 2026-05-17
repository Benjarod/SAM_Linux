<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class PromocionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $contenido;
    public $asuntoPersonalizado;
    public $smtpConfig;

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param array $data Información del destinatario
     * @param string $contenido Contenido HTML de la plantilla
     * @param string $asuntoPersonalizado Asunto del correo
     * @param array $smtpConfig Configuración SMTP específica
     */
    public function __construct($data, $contenido, $asuntoPersonalizado, array $smtpConfig)
    {
        $this->data = $data;
        $this->contenido = $contenido;
        $this->asuntoPersonalizado = $asuntoPersonalizado;
        $this->smtpConfig = $smtpConfig;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        // 🔄 Forzar que Laravel reconstruya el Mailer con la nueva configuración
        app()->forgetInstance('mail.manager');
        app()->forgetInstance('mailer');

        // ✅ Aplicar configuración SMTP específica antes de construir el mensaje
        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $this->smtpConfig['host'],
            'port'       => $this->smtpConfig['port'],
            'encryption' => $this->smtpConfig['encryption'],
            'username'   => $this->smtpConfig['username'],
            'password'   => $this->smtpConfig['password'],
        ]);

        Config::set('mail.from.address', $this->smtpConfig['from']);
        Config::set('mail.from.name', $this->smtpConfig['name']);

        return $this->subject($this->asuntoPersonalizado)
                    ->markdown('MailPromotion.promotionMail')
                    ->with([
                        'data' => $this->data,
                        'contenido' => $this->contenido,
                    ]);
    }
}
