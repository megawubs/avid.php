<?php
namespace Wubs\Avid;

use Illuminate\Support\ServiceProvider;

class AvidServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/assets/views', 'Avid');
    }

    public function register()
    {
        $this->app->singleton(Avid::class, function () {
            return new Avid();
        });

        $this->app->bind('avid', Avid::class);
    }
}
