<?php

namespace Taoran\Laravel\Jwt;

use Illuminate\Support\ServiceProvider;

class JwtAuthProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //注册路由
        $this->registerRoute();

        \Session::extend('jwt', function ($app) {
            // Return implementation of SessionHandlerInterface...
            return new JwtAuthSession();
        });

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

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('jwt', function () {
            return new JwtAuth;
        });

    }
}
