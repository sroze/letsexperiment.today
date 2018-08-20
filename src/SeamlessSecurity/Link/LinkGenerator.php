<?php

namespace App\SeamlessSecurity\Link;

use App\SeamlessSecurity\Guard\TokenGenerator;

class LinkGenerator
{
    private $tokenGenerator;

    public function __construct(TokenGenerator $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function generateLink(string $email, string $redirectionUrl) : string
    {
        return $redirectionUrl.'?token='.$this->tokenGenerator->generateToken($email);
    }
}
