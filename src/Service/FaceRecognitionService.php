<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FaceRecognitionService
{
    private string $pythonScriptPath;
    private string $usersImagePath;

    public function __construct(string $pythonScriptPath, string $usersImagePath)
    {
        $this->pythonScriptPath = $pythonScriptPath;
        $this->usersImagePath = $usersImagePath;
    }

    public function verifyFace(): bool
    {
        $pythonBinary = (stripos(PHP_OS, 'WIN') === 0) ? 'python' : 'python3';

        $process = new Process([$pythonBinary, $this->pythonScriptPath, $this->usersImagePath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput()) === "True"; // Vérifie si la sortie est "True"
    }
}
