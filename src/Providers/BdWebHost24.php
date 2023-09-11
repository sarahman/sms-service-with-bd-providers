<?php

namespace Sarahman\SmsService\Providers;

class BdWebHost24 extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['senderid'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880' . $matches[3];

        return [
            'contacts' => $recipient,
            'msg' => urlencode(preg_replace('/[^a-zA-Z0-9\.@!?&\-,%\(\):\"]/', ' ', $message)),
        ];
    }

    public function getValidationRules()
    {
        return [
            'senderid' => 'required',
            'api_key' => 'required',
            'type' => 'required',
            'contacts' => 'required|regex:/^8801[3456789]\d{8}$/',
            'msg' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        return [
            'success' => preg_match('/^SMS SUBMITTED\: ID \- .*$/i', $response),
            'response' => $response,
        ];
    }
}
