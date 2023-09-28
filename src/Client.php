<?php

namespace Sarahman\SmsService;

use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Sarahman\HttpRequestApiLog\Traits\WritesHttpLogs;
use Sarahman\SmsService\Interfaces\NeedsAuthenticationInterface;
use Sarahman\SmsService\Interfaces\ProviderInterface;
use Sarahman\SmsService\Providers;

class Client
{
    use WritesHttpLogs;

    const PROVIDER_BANGLALINK = Providers\Banglalink::class;
    const PROVIDER_BD_WEB_HOST_24 = Providers\BdWebHost24::class;
    const PROVIDER_BOOM_CAST = Providers\BoomCast::class;
    const PROVIDER_ELITBUZZ = Providers\Elitbuzz::class;
    const PROVIDER_GRAMEENPHONE = Providers\Grameenphone::class;
    const PROVIDER_NOVOCOM = Providers\Novocom::class;
    const PROVIDER_PAYSTATION = Providers\Paystation::class;
    const PROVIDER_ROBI = Providers\Robi::class;
    const PROVIDER_SSL = Providers\Ssl::class;
    const PROVIDER_VALUE_FIRST = Providers\ValueFirst::class;

    private $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
        $this->enableLogging = Config::get('sms-service-with-bd-providers::config.enable_api_call_logging', false);
    }

    /**
     * Return a SMS provider according to the given provider name.
     *
     * @param string $providerName
     * @param array $config
     * @param string $url
     * @return ProviderInterface
     */
    public static function getProvider($providerName = self::PROVIDER_SSL, array $config = [], $url = null)
    {
        switch ($providerName) {
            case self::PROVIDER_BANGLALINK:
            case self::PROVIDER_BD_WEB_HOST_24:
            case self::PROVIDER_BOOM_CAST:
            case self::PROVIDER_ELITBUZZ:
            case self::PROVIDER_GRAMEENPHONE:
            case self::PROVIDER_NOVOCOM:
            case self::PROVIDER_PAYSTATION:
            case self::PROVIDER_ROBI:
            case self::PROVIDER_SSL:
            case self::PROVIDER_VALUE_FIRST:
                return new $providerName($config, $url);

            default:
                throw new Exception('Invalid SMS provider name is given.');
        }
    }

    public function send($recipients, $message, array $params = [])
    {
        $log = ['sent' => [], 'failed' => []];
        is_array($recipients) || $recipients = [$recipients];

        foreach ($recipients as $recipient) {
            $options = ['url' => $this->provider->getUrl()];

            try {
                if (!$data = $this->provider->mapParams($recipient, $message, $params)) {
                    throw new Exception(json_encode('Failed to map the params.'), 422);
                }

                $data = array_merge($this->provider->getConfig(), $data);
                $validator = Validator::make($data, $this->provider->getValidationRules());

                if ($validator->fails()) {
                    throw new Exception(json_encode($validator->messages()->all()), 422);
                }

                $options = $this->prepareCurlOptions($data);
                $response = $this->provider->parseResponse($this->executeWithCurl($options));

                if (!$response->getStatus()) {
                    throw new Exception($response->getResponseString(), 500);
                }

                $log['sent'][$recipient] = $response->toArray();

                $this->log('POST', $options['url'], $options, new Response(200, [], $response->getResponseString()));
            } catch (Exception $e) {
                $errorCode = $e->getCode() >= 100 ? $e->getCode() : 500;
                $errorMessage = 422 != $errorCode ? $e->getMessage() : json_decode($e->getMessage(), true);
                $log['failed'][$recipient] = [
                    'success' => false,
                    'response' => $errorMessage,
                ];

                $this->log('POST', $options['url'], $options, new Response($errorCode, [], $errorMessage));
            }
        }

        return $this->getSummaryWithLogs($log);
    }

    public function sendWithFallback($recipients, $message, array $params = [])
    {
        $log = ['sent' => [], 'failed' => []];
        is_array($recipients) || $recipients = [$recipients];

        foreach ($recipients as $recipient) {
            $options = ['url' => $this->provider->getUrl()];

            try {
                if (!$data = $this->provider->mapParams($recipient, $message, $params)) {
                    throw new Exception(json_encode('Failed to map the params.'), 422);
                }

                $data = array_merge($this->provider->getConfig(), $data);
                $validator = Validator::make($data, $this->provider->getValidationRules());

                if ($validator->fails()) {
                    throw new Exception(json_encode($validator->messages()->all()), 422);
                }

                $options = $this->prepareCurlOptions($data);

                try {
                    $response = $this->executeWithCurl($options);
                } catch (Exception $e) {
                    $log['failed'][$recipient] = [
                        'success' => false,
                        'response' => $e->getMessage(),
                    ];
                    $response = '';
                }

                $response = $this->provider->parseResponse($response);

                if (!$response->getStatus()) {
                    $this->log('POST', $options['url'], $options, new Response(500, [], $response->getResponseString()));

                    //Resend sms
                    Log::info('SMS sending failed response!');

                    try {
                        $response = $this->provider->parseResponse($this->executeWithCurl($options));
                        Log::info('Second try of sending SMS', $response);

                        if (!$response->getStatus()) {
                            throw new Exception($response->getResponseString(), 500);
                        }
                    } catch (Exception $e) {
                        Log::error('Curl error response: ' . $e->getMessage());
                        throw $e;
                    }
                }

                $log['sent'][$recipient] = $response->toArray();

                $this->log('POST', $options['url'], $options, new Response(200, [], $response->getResponseString()));
            } catch (Exception $e) {
                $errorCode = $e->getCode() >= 100 ? $e->getCode() : 500;
                $errorMessage = 422 != $errorCode ? $e->getMessage() : json_decode($e->getMessage(), true);
                $log['failed'][$recipient] = [
                    'success' => false,
                    'response' => $errorMessage,
                ];

                $this->log('POST', $options['url'], $options, new Response($errorCode, [], $errorMessage));
            }
        }

        return $this->getSummaryWithLogs($log);
    }

    private function prepareCurlOptions(array $data)
    {
        $options = [
            'url' => $this->provider->getUrl(),
            'timeout' => 30,
        ];

        switch(get_class($this->provider)) {
            case self::PROVIDER_GRAMEENPHONE:
            case self::PROVIDER_NOVOCOM:
                $options += [
                    'httpheader' => ['Content-Type: application/json'],
                    'post' => 1,
                    'postfields' => json_encode($data),
                ];
                break;

            case self::PROVIDER_PAYSTATION:
                $options += [
                    'httpheader' => [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'user_id:' . $data['user_id'],
                        'password:' . $data['password'],
                    ],
                    'post' => 1,
                    'postfields' => json_encode($data),
                ];
                break;

            default:
                $options += [
                    'post' => count($data),
                    'postfields' => http_build_query($data),
                ];
        }

        if ($this->provider instanceof NeedsAuthenticationInterface) {
            $options['httpheader'][] = 'Authorization: Bearer ' . $this->provider->getAccessToken();
        }

        return $options;
    }

    private function executeWithCurl(array $options, $withHttpStatus = false)
    {
        $curlOptions = [];
        isset($options['returntransfer']) || $options['returntransfer'] = true;

        foreach ($options as $key => $value) {
            $option = 'CURLOPT_' . strtoupper($key);
            $curlOptions[constant($option)] = $value;
        }

        $ch = curl_init();

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        if ($response != true) {
            $eMsg = 'cURL Error # ' . curl_errno($ch) . ' | cURL Error Message: ' . curl_error($ch);

            curl_close($ch);

            throw new Exception($eMsg);
        }

        if ($withHttpStatus) {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        curl_close($ch);

        if (!$withHttpStatus) {
            return $response;
        }

        return [
            'httpStatusCode' => $httpStatusCode,
            'body' => $response,
        ];
    }

    private function getSummaryWithLogs(array $log)
    {
        $sent = count($log['sent']);
        $failed = count($log['failed']);

        return [
            'summary' => [
                'sent' => $sent,
                'failed' => $failed,
                'total' => $sent + $failed,
            ],
            'log' => $log,
        ];
    }
}
