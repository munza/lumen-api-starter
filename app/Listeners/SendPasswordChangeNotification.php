<?php

namespace App\Listeners;

use App\Events\UserUpdated;

class SendPasswordChangeNotification
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
     * @param  UserUpdated  $event
     * @return void
     */
    public function handle(UserUpdated $event)
    {
        if (!in_array('password', $event->changes)) {
            return;
        }

        // send email to notify the user of his password change event.
    }
}
