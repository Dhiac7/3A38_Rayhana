<?php
namespace App\Service;

use Symfony\Component\Mime\Email;

class EmailMessage
{
    private Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
