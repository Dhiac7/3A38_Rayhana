<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class SeatController extends AbstractController
{#[Route('/choisir-place', name: 'choose_seat')]
    public function index(PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findAll();
    
        return $this->render('seat/choose_seat.html.twig', [
            'places' => $places,
        ]);
    }
    
    #[Route('/reserve-place', name: 'reserve_seat', methods: ['POST'])]
public function reservePlace(Request $request, PlaceRepository $placeRepository, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $placeCode = $data['code'] ?? null;

    if (!$placeCode) {
        return new JsonResponse(['success' => false, 'message' => 'Code de place manquant.'], Response::HTTP_BAD_REQUEST);
    }

    $place = $placeRepository->findOneBy(['code' => $placeCode]);

    if (!$place) {
        return new JsonResponse(['success' => false, 'message' => 'Place non trouvée.'], Response::HTTP_NOT_FOUND);
    }

    if (!$place->getIsAvailable()) { // Utilisez getIsAvailable() au lieu de isAvailable()
        return new JsonResponse(['success' => false, 'message' => 'Cette place est déjà réservée.'], Response::HTTP_CONFLICT);
    }

    // Marquer la place comme réservée
    $place->setIsAvailable(false); // Utilisez setIsAvailable(false) pour désactiver la place
    $entityManager->flush();

    return new JsonResponse(['success' => true, 'message' => "La place $placeCode a été réservée avec succès !"]);
}
}    
?>
