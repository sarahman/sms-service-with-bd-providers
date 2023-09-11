<?php

namespace Sarahman\SmsService\Providers;

use SimpleXMLElement;

class Robi extends BaseProvider
{
    public function getUsername()
    {
        return $this->config['Username'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880' . $matches[3];

        return [
            'To' => $recipient,
            'Message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'Username' => 'required',
            'Password' => 'required',
            'From' => 'required|regex:/^8801[3456789]\d{8}$/',
            'To' => 'required|regex:/^8801[3456789]\d{8}$/',
            'Message' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        $response = new SimpleXMLElement($response);

        return [
            'success' => 0 == (string) $response->ErrorCode,
            'response' => $response->asXML(),
        ];
    }
}
