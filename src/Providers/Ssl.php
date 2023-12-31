<?php

namespace Sarahman\SmsService\Providers;

use Exception;
use Illuminate\Support\Facades\Log;
use Sarahman\SmsService\Response;
use SimpleXMLElement;

class Ssl extends BaseProvider
{
    public function getUrl()
    {
        return sprintf('%s/%s/server.php', parent::getUrl(), $this->config['sid']);
    }

    public function getUsername()
    {
        return $this->config['user'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880'.$matches[3];
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
            'user'    => 'required',
            'pass'    => 'required',
            'sid'     => 'required',
            'sms.0.0' => 'required|regex:/^8801[3456789]\d{8}$/',
            'sms.0.1' => 'required',
            'sms.0.2' => 'required',
        ];
    }

    public function parseResponse($response)
    {
        try {
            $xmlElement = new SimpleXMLElement($response);

            return new Response('SUCESSFULL' === strtoupper((string) $xmlElement->LOGIN), $response);
        } catch (Exception $exception) {
            Log::error($exception);

            return new Response(false, 'SSL did not respond!');
        }
    }
}
