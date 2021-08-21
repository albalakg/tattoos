<?php

namespace App\Listeners\Users;

use App\Domain\Helpers\MailService;
use App\Events\Users\NewUserEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewUserWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(MailService)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUser  $event
     * @return void
     */
    public function handle(NewUserEvent $event)
    {
        //
    }
}
