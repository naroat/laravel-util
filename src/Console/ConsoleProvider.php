<?php
namespace Taoran\Laravel\Console;

use Illuminate\Support\ServiceProvider;

class ConsoleProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommand();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function registerConsoleCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Taoran\Laravel\Console\CreateLogicCommand::class,
                \Taoran\Laravel\Console\CreateRepositoryCommand::class,
                \Taoran\Laravel\Console\CreateServiceCommand::class,
            ]);
        }
    }
}
