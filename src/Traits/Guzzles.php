<?php

namespace Sarahman\SmsService\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait Guzzles
{
    private $baseUri = '';
    private $timeout = 0;

    /**
     * Instantiates and returns the Guzzle client.
     *
     * @return Client
     */
    private function buildClient()
    {
        return new Client([
            'base_uri'    => $this->baseUri,
            'timeout'     => isset($this->timeout) ? $this->timeout : 0,
            'http_errors' => false,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Executes the guzzle request with given params and handle exception if occurred.
     *
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param array  $params
     *
     * @return ResponseInterface $response
     */
    final protected function makeRequestWithHandlingException(Client $client, $method, $url, array $params = [])
    {
        try {
            $response = $client->{$method}($url, $params);
        } catch (BadResponseException $e) {
            Log::error($e);
            $response = $e->getResponse();
        } catch (Exception $e) {
            Log::error($e);
            $response = new Response($e->getCode() >= 100 ? $e->getCode() : 500, [], $e->getMessage());
        }

        return $response;
    }

    /**
     * Parses the JSON response to an array.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    protected function parseJson(ResponseInterface $response)
    {
        $body = (string) $response->getBody();

        if (!empty($body)) {
            $body = json_decode($body, true);
        }

        return empty($body) ? [] : $body;
    }
}
