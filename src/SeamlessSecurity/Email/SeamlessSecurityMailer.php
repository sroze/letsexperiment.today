<?php

namespace App\SeamlessSecurity\Email;

use Symfony\Component\HttpFoundation\Request;

class SeamlessSecurityMailer
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendLoginEmail(string $email, string $link, Request $request)
    {
        try {
            $contents = $this->twig->render(
                'security/emails/login_link.html.twig',
                array('link' => $link)
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not send email', $e->getCode(), $e);
        }

        $message = (new \Swift_Message('Your login link for LetsExperiment.today'))
            ->setFrom('samuel@letsexperiment.today', 'Samuel from LetsExperiment.today')
            ->setTo($email)
            ->setBody($contents,'text/html');

        $this->mailer->send($message);
    }
}
