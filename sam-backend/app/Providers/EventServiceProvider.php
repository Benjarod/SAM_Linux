<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Aquí puedes registrar eventos personalizados si los usas
    ];

    public function boot(): void
    {
        //
    }
}
