<?php
namespace MegaWubs\Avid;

use Illuminate\Support\ServiceProvider;

class AvidServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Avid::class, function () {
            return new Avid();
        });
    }
}
