<?php

namespace App\SeamlessSecurity\Guard;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class TokenGenerator
{
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function generateToken(string $email)
    {
        try {
            return $this->jwtEncoder->encode([
                'email' => $email,
            ]);
        } catch (JWTEncodeFailureException $e) {
            throw new ServiceUnavailableHttpException(30, 'Could not create token', $e);
        }
    }
}
