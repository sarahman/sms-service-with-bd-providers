<?php

namespace Sarahman\SmsService\Interfaces;

interface NeedsAuthenticationInterface
{
    /**
     * Return the access token with managing it.
     *
     * @param bool $generate
     *
     * @return string
     */
    public function getAccessToken($generate = false);
}
