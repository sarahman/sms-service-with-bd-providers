<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class Paystation extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['user_id'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '0' . $matches[3];

        return [
            'type' => $this->config['type'],
            'number' => $recipient,
            'message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'user_id' => 'required',
            'password' => 'required',
            'type' => 'required',
            'number' => 'required|regex:/^01[3456789]\d{8}$/',
            'message' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response('success' === strtolower($parsedResponse->status), $response);
    }
}
