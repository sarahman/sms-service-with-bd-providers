<?php

namespace Sarahman\SmsService;

use Illuminate\Contracts\Support\Arrayable;

class Response implements Arrayable
{
    /**
     * Returns the status of the response as boolean.
     *
     * @var bool
     */
    private $status = false;

    /**
     * Returns the text of the response as string.
     *
     * @var string
     */
    private $responseString;

    /**
     * @param bool   $status
     * @param string $responseString
     */
    public function __construct($status, $responseString)
    {
        $this->status = $status;
        $this->responseString = $responseString;
    }

    public function toArray()
    {
        return [
            'status'   => $this->status,
            'response' => $this->responseString,
        ];
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getResponseString()
    {
        return $this->responseString;
    }
}
