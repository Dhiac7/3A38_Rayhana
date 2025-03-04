<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CommentAnalyzer
{
    private $httpClient;
    private $apiUser;
    private $apiSecret;

    public function __construct(HttpClientInterface $httpClient, string $apiUser = '1886208732', string $apiSecret = '6tfU9ncwxSqSwLWKMbMJkzWCPgVRSY8H')
    {
        $this->httpClient = $httpClient;
        $this->apiUser = $apiUser;
        $this->apiSecret = $apiSecret;
    }

    public function analyzeComment(string $text): ?array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.sightengine.com/1.0/text/check.json', [
                'body' => [
                    'text'       => $text,
                    'lang'       => 'en',
                    'categories' => 'profanity', // On se concentre uniquement sur la détection de gros mots
                    'mode'       => 'rules',
                    'api_user'   => $this->apiUser,
                    'api_secret' => $this->apiSecret
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            return ['error' => 'Erreur API : ' . $e->getMessage()];
        }
    }
}
