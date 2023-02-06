<?php

declare(strict_types=1);

namespace Minepic\Listeners\Account;

use Minepic\Events\Account\AccountCreatedEvent;
use Minepic\Models\AccountStats;

class AccountCreatedListener
{
    public function handle(AccountCreatedEvent $event): void
    {
        AccountStats::create([
            'uuid' => $event->getAccount()->uuid,
            'count_request' => 0,
            'request_at' => null,
        ]);
    }
}
