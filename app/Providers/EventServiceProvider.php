<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\UserCreated::class => [
            \App\Listeners\SendWelcomeEmail::class,
        ],

        \App\Events\UserUpdated::class => [
            \App\Listeners\SendPasswordChangeNotification::class,
        ],
    ];
}
