<?php

namespace Sarahman\SmsService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class SmsGatewayServiceProvider extends ServiceProvider
{
    protected $provider;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->provider = $this->getProvider();
    }

    public function boot()
    {
        if (method_exists($this->provider, 'boot')) {
            return $this->provider->boot();
        }
    }

    public function register()
    {
        // We have nothing to register here
    }

    private function getProvider()
    {
        if (version_compare(Application::VERSION, '5.0', '<')) {
            $provider = '\Sarahman\SmsService\ServiceProviderForLaravel4';
        } else {
            $provider = '\Sarahman\SmsService\ServiceProviderForLaravelRecent';
        }
        return new $provider($this->app);
    }
}
