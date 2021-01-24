<?php

declare(strict_types=1);

namespace Minepic\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Minepic\Events\Account\UsernameChangeEvent;
use Minepic\Listeners\Account\UsernameChangeListener;
use Minepic\Events\Account\AccountCreatedEvent;
use Minepic\Listeners\Account\AccountCreatedListener;
use Minepic\Events\Account\AccountImageServedEvent;
use Minepic\Listeners\Account\AccountImageServedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UsernameChangeEvent::class => [
            UsernameChangeListener::class,
        ],
        AccountCreatedEvent::class => [
            AccountCreatedListener::class,
        ],
        AccountImageServedEvent::class => [
            AccountImageServedListener::class,
        ],
    ];
}
