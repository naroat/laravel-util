<?php
namespace Taoran\Laravel\Helper;

use Illuminate\Support\ServiceProvider;

class HelperProvider extends ServiceProvider
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
        var_dump(123);
        require_once __DIR__ . 'Core.php';

    }

}