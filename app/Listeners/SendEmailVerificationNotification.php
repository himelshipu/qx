<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

class SendEmailVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user->hasVerifiedEmail()) {
            return;
        }

        $event->user->sendVerificationCodeNotification();
    }
}
