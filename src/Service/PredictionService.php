<?php
namespace App\Service;

use GuzzleHttp\Client;

class PredictionService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:5000/',  // Flask API URL
        ]);
    }

    public function predict(array $inputData)
    {
        $response = $this->client->post('/predict', [
            'json' => [
                'data' => $inputData,
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        return $result['prediction'];
    }
}