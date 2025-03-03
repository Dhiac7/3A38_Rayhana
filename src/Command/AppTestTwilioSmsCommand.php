<?php

// src/Command/AppTestTwilioSmsCommand.php
namespace App\Command;

use App\Service\TwilioService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test-sms')]
class AppTestTwilioSmsCommand extends Command
{
    private TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = "Test SMS Symfony 6.4 avec Twilio ✅";
        $this->twilioService->sendSms($_ENV['ADMIN_PHONE_NUMBER'], $message);

        $output->writeln('✅ SMS de test envoyé !');
        return Command::SUCCESS;
    }
}
