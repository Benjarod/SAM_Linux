<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSmtpSetting extends Model
{
    protected $fillable = [
        'user_id',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'email_from'
    ];
}
