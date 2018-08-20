<?php

namespace App\Emails;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class RenderAndSendEmails
{
    private $twig;
    private $mailer;

    public function __construct(\Twig_Environment $twig, Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function renderAndSend(string $recipient, string $subject, string $template, array $parameters)
    {
        try {
            $contents = $this->twig->render($template, $parameters);
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not send email', $e->getCode(), $e);
        }

        // Add style
        $contentsWithInlineStyle = (new CssToInlineStyles())->convert(
            $contents,
            $this->twig->render('experiment/emails/partials/style.css.twig')
        );

        $message = (new \Swift_Message($subject))
            ->setFrom('samuel@letsexperiment.today', 'LetsExperiment.today')
            ->setTo($recipient)
            ->setBody($contentsWithInlineStyle,'text/html');

        $headers = $message->getHeaders();
        $headers->addTextHeader('X-MC-Tags', $template);

        $this->mailer->send($message);
    }
}
