<?php

namespace Sarahman\SmsService\Providers;

use Sarahman\SmsService\Response;

class BoomCast extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['userName'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880' . $matches[3];

        return [
            'receiver' => $recipient,
            'message' => preg_replace('/[^a-zA-Z0-9\.@!?&\-,%\(\):\"]/', ' ', $message),
        ];
    }

    public function getValidationRules()
    {
        return [
            'userName' => 'required',
            'password' => 'required',
            'MsgType' => 'required',
            'masking' => 'required',
            'receiver' => 'required|regex:/^8801[3456789]\d{8}$/',
            'message' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $parsedResponse = json_decode($response);

        return new Response($parsedResponse[0]->success, $response);
    }
}
