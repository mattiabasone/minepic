<?php

declare(strict_types=1);

namespace Minepic\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Minepic\Events\Account\UsernameChangeEvent' => [
            'Minepic\Listeners\Account\UsernameChangeListener',
        ],
        'Minepic\Events\Account\AccountCreatedEvent' => [
            'Minepic\Listeners\Account\AccountCreatedListener',
        ],
        'Minepic\Events\Account\AccountImageServedEvent' => [
            'Minepic\Listeners\Account\AccountImageServedListener',
        ],
    ];
}
