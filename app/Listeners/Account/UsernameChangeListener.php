<?php

declare(strict_types=1);

namespace App\Listeners\Account;

use App\Events\Account\UsernameChangeEvent;
use App\Models\AccountNameChange;

class UsernameChangeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
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
