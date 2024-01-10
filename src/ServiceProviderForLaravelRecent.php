<?php
namespace Sarahman\SmsService;

use Illuminate\Support\ServiceProvider;

class ServiceProviderForLaravelRecent extends ServiceProvider
{
    public function boot()
    {
        // Publishes the configuration file
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('sms-service.php'),
        ], 'config');
    }

    public function register()
    {
        // Merges default configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/config.php',
            'sms-service'
        );
    }
}
