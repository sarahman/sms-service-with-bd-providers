<?php

namespace Sarahman\SmsService\Interfaces;

interface NeedsAuthenticationInterface
{
    /**
     * Return the access token with managing it.
     *
     * @param boolean $generate
     * @return string
     */
    public function getAccessToken($generate = false);
}
