<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use App\Service\EmailMessage;

class EmailMessageHandler
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(EmailMessage $message)
    {
        $email = $message->getEmail();
        $this->mailer->send($email);  // Send the email
    }
}

