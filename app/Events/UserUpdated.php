<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserUpdated extends Event
{
    use SerializesModels;

    public $user;

    public $changes;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     * @param array $changes
     *
     * @return void
     */
    public function __construct(User $user, array $changes)
    {
        $this->user = $user;
        $this->changes = $changes;
    }
}
