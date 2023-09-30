<?php

namespace Sarahman\SmsService\Providers;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Sarahman\SmsService\Interfaces\NeedsAuthenticationInterface;
use Sarahman\SmsService\Response;
use Sarahman\SmsService\Traits\Guzzles;

class ValueFirst extends BaseProvider implements NeedsAuthenticationInterface
{
    use Guzzles;

    private $baseUri = '';
    private $enableLogging = false;

    public function __construct(array $config = [], $url = null)
    {
        parent::__construct($config, $url);
        $this->enableLogging = Config::get('text-message.enable_api_call_logging', false);
    }

    public function getUrl()
    {
        return parent::getUrl().'/sendsms';
    }

    public function getUsername()
    {
        return $this->config['username'];
    }

    public function mapParams($recipient, $message, array $params = [])
    {
        if (!preg_match($this->recipientPattern, $recipient, $matches)) {
            return [];
        }

        $recipient = '880'.$matches[3];

        if (!array_key_exists('coding', $this->config) || $this->config['coding'] != 3) {
            $message = preg_replace('/[^a-zA-Z0-9\.@!?&\-,%\(\):\"]/', ' ', $message);
        }

        return [
            'to'   => $recipient,
            'from' => $this->config['from'],
            'text' => $message,
        ];
    }

    public function getValidationRules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
            'to'       => 'required|regex:/^8801[3456789]\d{8}$/',
            'text'     => 'required',
            'coding'   => 'integer',
        ];
    }

    public function parseResponse($response)
    {
        preg_match('/^Sent\.*/', $response, $matches);

        return new Response(is_array($matches) && array_key_exists(0, $matches), $response);
    }

    public function getAccessToken($generate = false)
    {
        $cacheKey = sprintf("%s_AccessToken:%s", __CLASS__, $this->getUsername());

        if (!$generate && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $api = $this->url.'/api/sendsms/token?action=generate';
        $request = [
            'headers' => [
                'Authorization' => ['Basic '.base64_encode($this->config['username'].':'.$this->config['password'])],
            ],
        ];

        try {
            $client = $this->buildClient();
            $response = $this->makeRequestWithHandlingException($client, $method = 'post', $api, $request);

            if (200 === $response->getStatusCode()) {
                $responseData = $this->parseJson($response);
                $api = $this->url.'/api/sendsms/token?action=enable&token=all';
                $request['form_params'] = ['token' => $responseData['token']];
                $response = $this->makeRequestWithHandlingException($client, $method = 'post', $api, $request);

                if (200 === $response->getStatusCode()) {
                    Cache::put($cacheKey, $responseData['token'], $responseData['expiryDate']);

                    return $responseData['token'];
                }
            }
        } catch (Exception $exception) {
            Log::error($exception);
        }

        return null;
    }
}
