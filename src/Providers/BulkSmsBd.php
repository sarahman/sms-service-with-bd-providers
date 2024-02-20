<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class BulkSmsBd extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['api_key'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880'.$matches[3];

        return [
            'number'  => $recipient,
            'message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'api_key'  => 'required',
            'type'     => 'required',
            'senderid' => 'required',
            'number'   => 'required|regex:/^8801[3456789]\d{8}$/',
            'message'  => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response(202 === $parsedResponse->response_code, $response);
    }
}
