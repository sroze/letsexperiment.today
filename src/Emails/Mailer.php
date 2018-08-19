<?php
namespace App\Emails;

interface Mailer
{
    public function send(\Swift_Mime_SimpleMessage $message);
}
