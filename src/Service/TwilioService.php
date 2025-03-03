<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private string $twilioSid;
    private string $twilioAuthToken;
    private string $twilioPhoneNumber;

    public function __construct(string $twilioSid, string $twilioAuthToken, string $twilioPhoneNumber)
    {
        $this->twilioSid = $twilioSid;
        $this->twilioAuthToken = $twilioAuthToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    public function sendSms(string $toPhoneNumber, string $message): void
    {
        $client = new Client($this->twilioSid, $this->twilioAuthToken);
        
        try {
            $client->messages->create(
                $toPhoneNumber,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            // Log l'erreur ou gérer l'exception
            echo "Erreur d'envoi SMS: " . $e->getMessage();
        }
    }
}
