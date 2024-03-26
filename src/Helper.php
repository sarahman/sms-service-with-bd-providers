<?php

namespace Sarahman\SmsService;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class Helper
{
    public static function getConfig($configName = null, $default = null)
    {
        $config = version_compare(Application::VERSION, '5.0', '<') ? 'sms-service-with-bd-providers::config' : 'sms-service';

        return Config::get($config . ($configName ? '.'.$configName : ''), $default);
    }
}
