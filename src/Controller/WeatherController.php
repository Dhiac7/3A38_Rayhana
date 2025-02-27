<?php

// src/Controller/WeatherController.php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/coordinates/{lat}/{lon}', name: 'weather_by_coordinates')]
    public function weatherByCoordinates(float $lat, float $lon, WeatherService $weatherService): Response
    {
        $weather = $weatherService->getWeatherByCoordinates($lat, $lon);

        return $this->render('weather/index.html.twig', [
            'weather' => $weather,
        ]);
    }
}

