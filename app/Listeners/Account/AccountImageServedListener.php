<?php

declare(strict_types=1);

namespace App\Listeners\Account;

use App\Events\Account\AccountImageServedEvent;
use App\Minecraft\MinecraftDefaults;
use App\Models\AccountStats;
use Carbon\Carbon;

class AccountImageServedListener
{
    /**
     * @param AccountImageServedEvent $event
     */
    public function handle(AccountImageServedEvent $event): void
    {
        if ($event->getAccount() !== null && $event->getAccount()->uuid !== MinecraftDefaults::UUID && env('STATS_ENABLED')) {
            AccountStats::query()
                ->where('uuid', $event->getAccount()->uuid)
                ->update([
                    'count_request' => app('db')->raw('count_request + 1'),
                    'request_at' => Carbon::now(),
                ]);
        }
    }
}
