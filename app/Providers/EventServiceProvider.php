<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 */
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
    ];
}