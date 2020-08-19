<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Carbon;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $event->user->email_verified_at = Carbon::now()->toDateTimeString();

        $event->user->save();
    }
}
