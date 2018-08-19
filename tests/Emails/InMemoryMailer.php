<?php

namespace App\Tests\Emails;

use App\Emails\Mailer;

class InMemoryMailer implements Mailer
{
    private $sent = [];

    public function send(\Swift_Mime_SimpleMessage $message)
    {
        $this->sent[] = $message;
    }

    /**
     * @return \Swift_Mime_SimpleMessage[]
     */
    public function getSent(): array
    {
        return $this->sent;
    }
}
