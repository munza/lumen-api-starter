<?php

namespace App\Providers;

use App\Events;
use App\Listeners;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Events\UserCreated::class => [
            Listeners\SendWelcomeEmail::class,
        ],

        Events\UserUpdated::class => [
            Listeners\SendPasswordChangeNotification::class,
        ],
    ];
}
