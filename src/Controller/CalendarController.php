<?php

// src/Controller/CalendarController.php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\AtelierRepository; // Assure-toi que tu as cette dépendance
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    private $atelierRepository;

    public function __construct(AtelierRepository $atelierRepository)
    {
        $this->atelierRepository = $atelierRepository;
    }

    #[Route('/calendar', name: 'calendar')]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

    #[Route('/api/events', name: 'api_events')]
    public function getEvents(AtelierRepository $atelierRepository): JsonResponse
    {
        $ateliers = $atelierRepository->findAll();

        $events = [];
        foreach ($ateliers as $atelier) {
            $events[] = [
                'title' => $atelier->getNom(),
                'start' => $atelier->getStartAt()->format('Y-m-d H:i:s'),
                'end' => $atelier->getEndAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($events);
    }


}