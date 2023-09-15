<?php

namespace Sarahman\SmsService\Providers;

class Novocom extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['ClientId'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880' . $matches[3];

        return [
            'MobileNumbers' => $recipient,
            'Message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'ClientId' => 'required',
            'ApiKey' => 'required',
            'SenderId' => 'required',
            'MobileNumbers' => 'required|regex:/^8801[3456789]\d{8}$/',
            'Message' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return [
            'success' => 0 === (int) $parsedResponse->ErrorCode,
            'response' => $response,
        ];
    }
}
