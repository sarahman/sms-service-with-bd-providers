<?php

namespace Sarahman\SmsService\Interfaces;

use Sarahman\SmsService\Response;

interface ProviderInterface
{
    public function __construct(array $config, $url = null);

    /**
     * Returns the config of a specific provider.
     *
     * @return array
     */
    public function getConfig();

    /**
     * Returns the base url of a specific provider.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Returns the username of a specific provider.
     *
     * @return string
     */
    public function getUsername();

    /**
      * Returns the mapped data of the given data.
      *
      * @param array|string $recipient
      * @param string $message
      * @param array $params
      * @return array
      */
    public function mapParams($recipient, $message, array $params);

    /**
      * Returns the validation rules for the sms data.
      *
      * @return array
      */
    public function getValidationRules();

    /**
      * Parses the API/Curl response and returns a common format.
      *
      * @param string $response
      * @return Response
      */
    public function parseResponse($response);
}
