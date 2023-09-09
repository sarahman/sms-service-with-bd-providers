<?php

namespace Sarahman\SmsService\Providers;

use Exception;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class Ssl extends BaseProvider
{
    public function getUrl()
    {
        return sprintf("%s/%s/server.php", parent::getUrl(), $this->config['sid']);
    }

    public function getUsername()
    {
        return $this->config['user'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match('/^(00|\+)?(8{2})?0?([0-9]{10})$/i', $recipient, $matches)) {
            return false;
        }

        $recipient = '880' . $matches[3];
        $clientRefId = !array_key_exists('id', $params) ? uniqid() : $params['id'];

        return [
            'sms' => [
                [
                    0 => $recipient,
                    1 => preg_replace(['/\'/', '/^\&/', '/(\.\s?)(\&)/', '/\&/'], [' ', 'And', '$1And', 'and'], $message),
                    2 => $clientRefId,
                ],
            ],
        ];
    }

    public function getValidationRules()
    {
        return [
            'user' => 'required',
            'pass' => 'required',
            'sid' => 'required',
            'sms.0.0' => 'required|regex:/^8801[3456789]\d{8}$/',
            'sms.0.1' => 'required',
            'sms.0.2' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        try {
            $response = new SimpleXMLElement($response);

            return [
                'success' => 'SUCESSFULL' === strtoupper((string) $response->LOGIN),
                'response' => $response->asXML(),
            ];
        } catch (Exception $exception) {
            Log::error($exception);

            return [
                'success' => false,
                'response' => 'SSL not responded',
            ];
        }
    }
}
