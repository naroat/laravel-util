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
        require_once __DIR__ . '/Core.php';
        require_once __DIR__ . '/String.php';
        require_once __DIR__ . '/Time.php';
        require_once __DIR__ . '/Password.php';
    }

}