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
