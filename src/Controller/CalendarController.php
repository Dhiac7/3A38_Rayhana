<?php

// src/Controller/CalendarController.php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

use App\Repository\AtelierRepository; // Assure-toi que tu as cette dépendance
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CalendarController extends AbstractController
{
    private $atelierRepository;

    public function __construct(AtelierRepository $atelierRepository)
    {
        $this->atelierRepository = $atelierRepository;
    }

    #[Route('/calendar', name: 'calendar')]
    public function index( SessionInterface $session,EntityManagerInterface $entityManager): Response
    {  $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        return $this->render('calendar/index.html.twig', [
            'loggedInUser' => $loggedInUser,

        ]);

    }

    #[Route('/api/events', name: 'api_events', methods: ['GET'])]
    public function getEvents(AtelierRepository $atelierRepository): JsonResponse
    {   
        $ateliers = $atelierRepository->findAll();
        $events = [];
    
        foreach ($ateliers as $atelier) {
            $events[] = [
                'title' => $atelier->getTitle(), // Assure-toi que getTitle() existe bien
                'start' => $atelier->getStartAt()->format('Y-m-d\TH:i:s'),
                'end' => $atelier->getEndAt() ? $atelier->getEndAt()->format('Y-m-d\TH:i:s') : null,
            ];
        }
    
        return new JsonResponse($events);
    }
    


}