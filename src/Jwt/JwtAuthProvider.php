<?php


namespace Taoran\Laravel\Jwt;

use Illuminate\Support\ServiceProvider;

class JwtAuthProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoute();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * 注册路由
     */
    public function registerRoute()
    {
        if (!$this->app->routesAreCached()) {
            \Route::group(['namespace' => '\Taoran\Laravel\Jwt'], function () {
                //初始化
                \Route::get('api/init', 'JwtAuth@routeInit');
            });
        }
    }
}