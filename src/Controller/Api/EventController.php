<?php

namespace App\Controller\Api;

use App\Repository\AtelierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/api/events', name: 'api_events', methods: ['GET'])]
    public function getEvents(AtelierRepository $atelierRepository): JsonResponse
    {
        // Récupérez tous les ateliers depuis la base de données
        $ateliers = $atelierRepository->findAll();

        // Transformez les ateliers en tableau pour FullCalendar
        $events = [];
        foreach ($ateliers as $atelier) {
            $events[] = [
                'title' => $atelier->getTitle(),
                'start' => $atelier->getStartAt()->format('Y-m-d\TH:i:s'),
                'end' => $atelier->getEndAt() ? $atelier->getEndAt()->format('Y-m-d\TH:i:s') : null,
            ];
        }

        // Retourne les événements au format JSON
        return $this->json($events);
    }
}