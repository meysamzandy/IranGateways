<?php

namespace MeysamZnd\IranGateways;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/iran-gateways.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('iran-gateways.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'iran-gateways'
        );

        $this->app->bind('iran-gateways', function () {
            return new MellatBank();
        });
    }
}
