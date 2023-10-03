<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class SslPlus extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['api_token'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880'.$matches[3];

        return [
            'csms_id' => isset($params['csms_id']) ? $params['csms_id'] : rand(100000, 999999),
            'msisdn'  => $recipient,
            'sms'     => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'api_token' => 'required',
            'sid'       => 'required',
            'csms_id'   => 'required',
            'msisdn'    => 'required|regex:/^8801[3456789]\d{8}$/',
            'sms'       => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response('success' === strtolower($parsedResponse->status) && 200 === $parsedResponse->status_code, $response);
    }
}
