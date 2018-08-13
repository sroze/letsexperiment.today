<?php

namespace App\Controller;

use App\SeamlessSecurity\Email\SeamlessSecurityMailer;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SeamlessSecurityController extends Controller
{
    private $jwtEncoder;
    private $userProvider;
    private $urlGenerator;
    private $securityMailer;

    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        UserProviderInterface $userProvider,
        UrlGeneratorInterface $urlGenerator,
        SeamlessSecurityMailer $securityMailer
    )
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
        $this->securityMailer = $securityMailer;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $email = $request->get('email');
        if (empty($email)) {
            throw new BadRequestHttpException('Email is invalid');
        }

        $url = $request->get('redirect_url', $this->urlGenerator->generate('home'));

        try {
            $token = $this->jwtEncoder->encode([
                'email' => $email,
            ]);
        } catch (JWTEncodeFailureException $e) {
            throw new ServiceUnavailableHttpException(30, 'Could not create token', $e);
        }

        $link = $url.'?token='.$token;

        $this->securityMailer->sendLoginEmail($email, $link, $request);


        return $this->render('Security/email_sent.html.twig', [
            'link' => $link,
        ]);
    }
}
