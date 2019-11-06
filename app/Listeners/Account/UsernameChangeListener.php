<?php

namespace App\Listeners\Account;

use App\Events\Account\UsernameChangeEvent;
use App\Models\AccountNameChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UsernameChangeListener
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
     * @param  \App\Events\Account\UsernameChangeEvent  $event
     * @return void
     */
    public function handle(UsernameChangeEvent $event): void
    {
        AccountNameChange::create([
            'uuid' => $event->getUuid(),
            'prev_name' => $event->getPreviousName(),
            'new_name' => $event->getNewName(),
        ]);
    }
}
