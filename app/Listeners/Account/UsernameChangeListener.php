<?php

declare(strict_types=1);

namespace Minepic\Listeners\Account;

use Minepic\Events\Account\UsernameChangeEvent;
use Minepic\Models\AccountNameChange;

class UsernameChangeListener
{
    /**
     * Handle the event.
     *
     * @param UsernameChangeEvent $event
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
