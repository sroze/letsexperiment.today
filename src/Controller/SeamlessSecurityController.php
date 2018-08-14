<?php

namespace App\Controller;

use App\SeamlessSecurity\Email\SeamlessSecurityMailer;
use App\SeamlessSecurity\Link\LinkGenerator;
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
    private $userProvider;
    private $urlGenerator;
    private $securityMailer;
    private $linkGenerator;

    public function __construct(
        UserProviderInterface $userProvider,
        UrlGeneratorInterface $urlGenerator,
        SeamlessSecurityMailer $securityMailer,
        LinkGenerator $linkGenerator
    )
    {
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
        $this->securityMailer = $securityMailer;
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @Route("/login", methods={"GET"})
     */
    public function loginForm()
    {
        return $this->render('security/login_form.html.twig');
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

        $url = $request->get('redirect_url', $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $link = $this->linkGenerator->generateLink($email, $url);

        $this->securityMailer->sendLoginEmail($email, $link, $request);

        return $this->render('security/email_sent.html.twig', [
            'link' => $link,
        ]);
    }
}
