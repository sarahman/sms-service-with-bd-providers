# SMS Gateway Library for Various Bangladesh SMS Providers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sarahman/sms-service-with-bd-providers.svg?style=flat-square)](https://packagist.org/packages/sarahman/sms-service-with-bd-providers)
[![Build Status](https://img.shields.io/travis/sarahman/sms-service-with-bd-providers/master.svg?style=flat-square)](https://travis-ci.org/sarahman/sms-service-with-bd-providers)
[![Quality Score](https://img.shields.io/scrutinizer/g/sarahman/sms-service-with-bd-providers.svg?style=flat-square)](https://scrutinizer-ci.com/g/sarahman/sms-service-with-bd-providers)
[![StyleCI](https://styleci.io/repos/686400823/shield)](https://styleci.io/repos/686400823)
[![Total Downloads](https://img.shields.io/packagist/dt/sarahman/sms-service-with-bd-providers.svg?style=flat-square)](https://packagist.org/packages/sarahman/sms-service-with-bd-providers)

Simple php library to interact with various SMS gateways to send SMS to the Bangladeshi mobile users with storing SMS request logs.

## Installation

Please install this library with `composer`. Run the following composer command to add this library.

```bash
composer require sarahman/sms-service-with-bd-providers
```

Next, you need to install the service provider:

```php
// app/config/app.php
    .....
    .....
    'providers' => [
        ...
        Sarahman\SmsService\SmsGatewayServiceProvider::class,
    ],
    .....
    .....
```

You can publish the config file with:

```bash
php artisan config:publish sarahman/sms-service-with-bd-providers
```

This is the contents of the published config file:

```php
<?php

use Sarahman\SmsService\Client;

return [
    'default' => [
        'provider' => Client::PROVIDER_SSL,
    ],

    'providers' => [
        Client::PROVIDER_BANGLALINK => [
            'url' => 'https://vas.banglalinkgsm.com/sendSMS/sendSMS',
            'userID' => '',
            'passwd' => '',
            'sender' => '',
        ],
        Client::PROVIDER_BD_WEB_HOST_24 => [
            'url' => 'http://sms.bdwebhost24.com/smsapi',
            'senderid' => '',
            'api_key' => '',
            'type' => 'text',
        ],
        Client::PROVIDER_BOOM_CAST => [
            'url' => 'http://api.boom-cast.com/boomcast/WebFramework/boomCastWebService/externalApiSendTextMessage.php',
            'userName' => '',
            'password' => '',
            'MsgType' => 'TEXT',
            'masking' => 'S.H.P.L',
        ],
        Client::PROVIDER_ELITBUZZ => [
            'url' => 'https://msg.elitbuzz-bd.com/smsapi',
            'api_key' => '',
            'senderid' => '',
            'type' => 'text',
        ],
        Client::PROVIDER_GRAMEENPHONE => [
            'url' => 'https://cmp.grameenphone.com/gpcmpapi/messageplatform/controller.home',
            'username' => '',
            'password' => '',
            'countrycode' => '880',
            'cli' => '',
            'apicode' => 1,
            'messagetype' => 1, // 1: Text; 2: Flash; 3: Unicode (Bangla)
            'messageid' => 0,
        ],
        Client::PROVIDER_NOVOCOM => [
            'url' => 'https://sms.novocom-bd.com/api/v2/SendSMS',
            'ApiKey' => '',
            'ClientId' => '',
            'SenderId' => '',
        ],
        Client::PROVIDER_PAYSTATION => [
            'url' => 'https://sms.shl.com.bd/sendsms',
            'user_id' => '',
            'password' => '',
            'type' => 'text',
        ],
        Client::PROVIDER_ROBI => [
            'url' => 'https://bmpws.robi.com.bd/ApacheGearWS/SendTextMessage',
            'Username' => '',
            'Password' => '',
            'From' => '',
        ],
        Client::PROVIDER_SSL => [
            'url' => 'http://sms.sslwireless.com/pushapi',
            'user' => '',
            'pass' => '',
            'sid' => '',
        ],
        Client::PROVIDER_VALUE_FIRST => [
            'url' => 'http://www.myvaluefirst.com/smpp/sendsms',
            'username' => '',
            'password' => '',
            'from' => '',
            'coding' => 3, // Unicode allows or not
        ],
    ],
    'enable_api_call_logging' => false,
];
```

Now, you need to set the default credentials of various SMS providers which are needed to be used.

**N.B.:** This library directly depends on the package: `sarahman/laravel-http-request-api-log`. Please follow [this link](https://github.com/sarahman/laravel-http-request-api-log/blob/master/README.md) to know the usages of this package.

## Usages

```php
<?php

use Sarahman\SmsService\Client;

require "vendor/autoload.php";

// Instantiate with default config
$smsSender = new Client(Client::getProvider(Client::PROVIDER_SSL));

// Or instantiate with custom config
$smsSender = new Client(Client::getProvider(Client::PROVIDER_SSL, [
    'user' => 'SSL_WIRELESS_USERNAME',
    'pass' => 'SSL_WIRELESS_PASSWORD',
    'sid' => 'SSL_WIRELESS_SID',
], 'SSL_WIRELESS_URL'));

try {
    $response = $smsSender->send($mobile, $message);

    if ($response['summary']['sent'] == $response['summary']['total']) {
        // Do for the successful response.
    } else {
        // Do for the failed response.
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
```

## Supported Providers

- [Banglalink SMS Service, Bangladesh](https://www.banglalinkgsm.com)
- [BD Web Host 24 SMS Service, Bangladesh](https://www.bdwebhost24.com)
- [BoomCast, Bangladesh](https://www.boomcast.io)
- [Elitbuzz Technologies Ltd., Bangladesh](https://elitbuzz-bd.com)
- [Grameenphone Limited, Bangladesh](https://www.grameenphone.com/business/enterprise-solutions/messaging)
- [NovoCom Limited, Bangladesh](https://www.novocom-bd.com)
- [PayStation Service Hub Limited, Bangladesh](https://merchant.paystation.com.bd)
- [Robi Axiata Limited, Bangladesh](https://www.robi.com.bd/en/business/mobile-services/corporate-solutions)
- [SSL Wireless Limited (v2 & v3), Bangladesh](https://www.sslwireless.com)
- [ValueFirst Digital Media Private Limited, Bangladesh](https://www.vfirst.com)

## For Other Gateway Providers

If you have your own SMS gateway and you provide API. Please build your own provider and send us pull request. We will add those here too.

To build your own provider, please follow `src/Interfaces/ProviderInterface.php`.

If you have any questions, please feel free to create an issue or write us at [aabid048@gmail.com](mailto:aabid048@gmail.com)

## Contributions

Feel free to contribute in this library. Add your own provider and send us [pull requests](https://github.com/sarahman/sms-service-with-bd-providers/pulls).

## Security Issues

If you discover any security related issues, please feel free to create an issue in the [issue tracker](https://github.com/sarahman/sms-service-with-bd-providers/issues) or write us at [aabid048@gmail.com](mailto:aabid048@gmail.com).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
