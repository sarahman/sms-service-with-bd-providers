<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

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

        $recipient = '880'.$matches[3];

        return [
            'MobileNumbers' => $recipient,
            'Message'       => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'ClientId'      => 'required',
            'ApiKey'        => 'required',
            'SenderId'      => 'required',
            'MobileNumbers' => 'required|regex:/^8801[3456789]\d{8}$/',
            'Message'       => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response(0 === (int) $parsedResponse->ErrorCode, $response);
    }
}
