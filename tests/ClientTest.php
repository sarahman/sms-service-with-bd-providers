<?php

namespace Tests;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Mockery;
use PHPUnit_Framework_TestCase;
use Sarahman\SmsService\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->config = require_once __DIR__.'/../src/config/config.php';
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.'.Client::PROVIDER_SSL)->andReturn(array_get($this->config, 'providers.'.Client::PROVIDER_SSL));
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.enable_api_call_logging', false)->andReturn(array_get($this->config, 'enable_api_call_logging'));
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_default_sms_provider()
    {
        $provider = Client::getProvider();

        $this->assertInstanceOf(Client::PROVIDER_SSL, $provider);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_the_provided_sms_provider_to_be_valid()
    {
        $provider = Client::getProvider(Client::PROVIDER_SSL);

        $this->assertInstanceOf(Client::PROVIDER_SSL, $provider);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_the_provided_sms_provider_to_be_invalid()
    {
        $this->setExpectedException(Exception::class, 'Invalid SMS provider name is given.');
        Client::getProvider(Client::PROVIDER_SSL.'2');
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_sending_sms_through_ssl()
    {
        $messageBag = new MessageBag();

        Validator::shouldReceive('make')->once()->andReturn(Mockery::mock(['fails' => false, 'messages' => $messageBag]));

        $provider = new Client(Client::getProvider(Client::PROVIDER_SSL));
        $result = $provider->send('01914886226', 'Alhamdulillah!');

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('log', $result);
        $this->assertEquals($result['summary']['sent'], $result['summary']['total']);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_sending_sms_through_ssl_plus()
    {
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.'.Client::PROVIDER_SSL_PLUS)->andReturn(array_get($this->config, 'providers.'.Client::PROVIDER_SSL_PLUS));

        $messageBag = new MessageBag();

        Validator::shouldReceive('make')->once()->andReturn(Mockery::mock(['fails' => false, 'messages' => $messageBag]));

        $provider = new Client(Client::getProvider(Client::PROVIDER_SSL_PLUS));
        $result = $provider->send('01914886226', 'Alhamdulillah!');

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('log', $result);
        $this->assertEquals($result['summary']['sent'], $result['summary']['total']);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_sending_sms_through_ssl_plus_with_reference()
    {
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.'.Client::PROVIDER_SSL_PLUS)->andReturn(array_get($this->config, 'providers.'.Client::PROVIDER_SSL_PLUS));

        $messageBag = new MessageBag();

        Validator::shouldReceive('make')->once()->andReturn(Mockery::mock(['fails' => false, 'messages' => $messageBag]));

        $provider = new Client(Client::getProvider(Client::PROVIDER_SSL_PLUS));
        $result = $provider->send('01914886226', 'Alhamdulillah!', ['csms_id' => 'ref-'.uniqid()]);

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('log', $result);
        $this->assertEquals($result['summary']['sent'], $result['summary']['total']);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_sending_sms_through_elitbuzz()
    {
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.'.Client::PROVIDER_ELITBUZZ)->andReturn(array_get($this->config, 'providers.'.Client::PROVIDER_ELITBUZZ));

        $messageBag = new MessageBag();

        Validator::shouldReceive('make')->once()->andReturn(Mockery::mock(['fails' => false, 'messages' => $messageBag]));

        $provider = new Client(Client::getProvider(Client::PROVIDER_ELITBUZZ));
        $result = $provider->send('01914886226', 'Alhamdulillah!');

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('log', $result);
        $this->assertEquals($result['summary']['sent'], $result['summary']['total']);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_checks_sending_sms_through_bulksmsbd()
    {
        Config::shouldReceive('get')->once()->with('sms-service-with-bd-providers::config.providers.'.Client::PROVIDER_BULK_SMS_BD)->andReturn(array_get($this->config, 'providers.'.Client::PROVIDER_BULK_SMS_BD));

        $messageBag = new MessageBag();

        Validator::shouldReceive('make')->once()->andReturn(Mockery::mock(['fails' => false, 'messages' => $messageBag]));

        $provider = new Client(Client::getProvider(Client::PROVIDER_BULK_SMS_BD));
        $result = $provider->send('01914886226', 'Alhamdulillah!');

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('log', $result);
        $this->assertEquals($result['summary']['sent'], $result['summary']['total']);
    }
}
