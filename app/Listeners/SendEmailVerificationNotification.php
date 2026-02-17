<?php

namespace App\Listeners;

use App\Models\User;
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
        /** @var User $user */
        $user = $event->user;
        
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendVerificationCodeNotification();
    }
}
