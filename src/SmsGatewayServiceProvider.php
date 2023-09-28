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
    }

    public function register()
    {
        //
    }
}
