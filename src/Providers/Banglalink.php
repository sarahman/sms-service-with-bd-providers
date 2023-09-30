<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class Banglalink extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['userID'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880'.$matches[3];

        return [
            'msisdn'  => $recipient,
            'message' => preg_replace('/[^a-zA-Z0-9\.@!?&\-,%\(\):\"]/', ' ', $message),
        ];
    }

    public function getValidationRules()
    {
        return [
            'userID'  => 'required',
            'passwd'  => 'required',
            'msisdn'  => 'required|regex:/^8801[3456789]\d{8}$/',
            'message' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        preg_match('/^(success count \: ([0-9])*) and (fail count \: ([0-9])*)/i', $response, $matches);

        return new Response(is_array($matches) && array_key_exists(2, $matches) && $matches[2] == 1, $response);
    }
}
