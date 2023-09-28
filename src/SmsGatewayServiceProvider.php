<?php

namespace Sarahman\SmsService;

use Illuminate\Support\ServiceProvider;

class SmsGatewayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->package('sarahman/sms-service-with-bd-providers', null, __DIR__);
        $this->package('sarahman/laravel-http-request-api-log', null, __DIR__ .'/../../laravel-http-request-api-log/src');
    }

    public function register()
    {
        //
    }
}
