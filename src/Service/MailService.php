<?php
namespace App\Service;

use Twilio\Rest\Client;

class MailService
{
    private $twilio;
    private $twilioPhoneNumber;

    public function __construct()
    {
        $this->twilio = new Client("ACef54ff1e19dd6a9e595e459348b4b49a","45359051eae29c02d0ea1c01b4c344dd");
        $this->twilioPhoneNumber = "+15737634449";
    }

    public function sendSms(string $to, string $message): void
    {
        try {
            $this->twilio->messages->create(
                $to,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Failed to send SMS: " . $e->getMessage());
        }
    }
}