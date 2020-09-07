<?php

declare(strict_types=1);

namespace App\Listeners\Account;

use App\Events\Account\AccountCreatedEvent;
use App\Models\AccountStats;

class AccountCreatedListener
{
    /**
     * @param AccountCreatedEvent $event
     */
    public function handle(AccountCreatedEvent $event): void
    {
        AccountStats::create([
            'uuid' => $event->getAccount()->uuid,
            'count_request' => 0,
            'request_at' => null,
        ]);
    }
}
