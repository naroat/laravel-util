<?php
namespace Taoran\Laravel;

use Illuminate\Support\ServiceProvider;

class BaseProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->register();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('taoran_response', function () {
            return new \Taoran\Laravel\Response();
        });
    }

}
