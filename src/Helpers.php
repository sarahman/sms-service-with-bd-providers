<?php

namespace Sarahman\SmsService;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class Helpers
{
    public static function getSMSConfig($configName)
    {
        if (version_compare(Application::VERSION, '5.0', '<')) {
            return Config::get('sms-service-with-bd-providers::config.providers.'.$configName);
        } else {
            return Config::get('sms-service.providers.'.$configName);
        }
    }
}
