<?php

namespace App\Listeners;

use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoginUserListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        SendEmailJob::dispatch($event->user);
    }
}
