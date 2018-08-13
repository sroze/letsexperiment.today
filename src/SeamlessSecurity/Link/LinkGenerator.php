<?php

namespace App\SeamlessSecurity\Link;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class LinkGenerator
{
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function generateLink(string $email, string $redirectionUrl) : string
    {
        try {
            $token = $this->jwtEncoder->encode([
                'email' => $email,
            ]);
        } catch (JWTEncodeFailureException $e) {
            throw new ServiceUnavailableHttpException(30, 'Could not create token', $e);
        }

        return $redirectionUrl.'?token='.$token;
    }
}
