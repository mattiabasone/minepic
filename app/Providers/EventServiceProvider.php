<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Account\UsernameChangeEvent' => [
            'App\Listeners\Account\UsernameChangeListener',
        ],
        'App\Events\Account\AccountCreatedEvent' => [
            'App\Listeners\Account\AccountCreatedListener',
        ],
        'App\Events\Account\AccountImageServedEvent' => [
            'App\Listeners\Account\AccountImageServedListener',
        ],
    ];
}
