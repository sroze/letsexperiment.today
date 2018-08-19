<?php

namespace App\Emails;

class SwiftMailer implements Mailer
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(\Swift_Mime_SimpleMessage $message)
    {
        return $this->mailer->send($message);
    }
}