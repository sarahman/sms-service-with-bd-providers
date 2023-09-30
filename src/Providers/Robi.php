<?php

namespace Sarahman\SmsService\Providers;

use Exception;
use Illuminate\Support\Facades\Log;
use Sarahman\SmsService\Response;
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

        $recipient = '880'.$matches[3];

        return [
            'To'      => $recipient,
            'Message' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'Username' => 'required',
            'Password' => 'required',
            'From'     => 'required|regex:/^8801[3456789]\d{8}$/',
            'To'       => 'required|regex:/^8801[3456789]\d{8}$/',
            'Message'  => 'required',
        ];
    }

    public function parseResponse($response)
    {
        try {
            $xmlElement = new SimpleXMLElement($response);

            return new Response(0 == (string) $xmlElement->ErrorCode, $response);
        } catch (Exception $exception) {
            Log::error($exception);

            return new Response(false, 'Robi did not respond!');
        }
    }
}
