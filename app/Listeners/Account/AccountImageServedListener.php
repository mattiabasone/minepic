<?php

declare(strict_types=1);

namespace Minepic\Listeners\Account;

use Carbon\Carbon;
use Minepic\Events\Account\AccountImageServedEvent;
use Minepic\Models\AccountStats;

class AccountImageServedListener
{
    /**
     * @param AccountImageServedEvent $event
     */
    public function handle(AccountImageServedEvent $event): void
    {
        if ($event->getAccount() !== null && env('STATS_ENABLED')) {
            AccountStats::query()
                ->where('uuid', $event->getAccount()->uuid)
                ->update([
                    'count_request' => app('db')->raw('count_request + 1'),
                    'request_at' => Carbon::now(),
                ]);
        }
    }
}
