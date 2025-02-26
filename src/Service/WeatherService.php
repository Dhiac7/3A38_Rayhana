<?php 

// src/Service/WeatherService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getWeatherByCoordinates(float $lat, float $lon): array
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$this->apiKey}&units=metric";

        $response = $this->client->request('GET', $url);
        return $response->toArray();
    }
}
