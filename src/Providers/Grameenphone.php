<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class Grameenphone extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['username'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '0' . $matches[3];

        return [
            'msisdn' => $recipient,
            'message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
            'apicode' => 'required',
            'msisdn' => 'required|regex:/^01[3456789]\d{8}$/',
            'countrycode' => 'required',
            'cli' => 'required',
            'messagetype' => 'required|integer',
            'message' => 'required',
            'messageid' => 'required|integer',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response(200 == $parsedResponse->statusCode, $response);
    }
}
