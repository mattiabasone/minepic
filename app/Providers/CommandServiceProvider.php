<?php namespace App\Providers;

use App\Console\Commands\CheckUuid;
use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CleanAccountsTable;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.minepic.clean-accounts', function () {
            return new CleanAccountsTable;
        });

        $this->app->singleton('command.minepic.check-uuid', function () {
            return new CheckUuid;
        });

        $this->commands([
            'command.minepic.clean-accounts',
            'command.minepic.check-uuid',
        ]);
    }
}