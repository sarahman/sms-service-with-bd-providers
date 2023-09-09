<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use PHPUnit_Framework_TestCase;
use Sarahman\SmsService\Client;
use Sarahman\SmsService\Providers\Ssl;

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = require __DIR__ . '/../src/config/config.php';
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.' . Ssl::class)->andReturn(array_get($this->config, 'providers.' . Ssl::class));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_checks_default_sms_provider()
    {
        $provider = Client::getProvider();

        $this->assertInstanceOf(Ssl::class, $provider);
    }
}