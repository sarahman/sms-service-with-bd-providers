<?php

namespace Sarahman\SmsService\Providers;

use Illuminate\Support\Facades\Config;
use Sarahman\SmsService\Interfaces\ProviderInterface;

abstract class BaseProvider implements ProviderInterface
{
    protected $configName;
    protected $config;
    protected $url;
    protected $recipientPattern = '/^(00|\+)?(8{2})?0?([0-9]{10})$/i';

    public function __construct(array $config = [], $url = null)
    {
        $this->loadConfigFromFile();

        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->config[$key] = $value;
            }
        }

        $this->extractUrlFromConfigAndSet($url);
    }

    public function setConfigName($configName)
    {
        $this->configName = $configName;

        return $this->loadConfigFromFile();
    }

    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    private function loadConfigFromFile()
    {
        $this->configName = !is_null($this->configName) ? $this->configName : get_class($this);
        $this->config = Config::get('sms-service-with-bd-providers::config.providers.'.$this->configName);

        $this->extractUrlFromConfigAndSet();

        return $this;
    }

    private function extractUrlFromConfigAndSet($url = null)
    {
        if (isset($this->config['url'])) {
            $url = $this->config['url'];
            unset($this->config['url']);
        }

        is_null($url) || $this->setUrl($url);
    }
}
