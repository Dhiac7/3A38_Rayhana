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
{
    #[Route('/choisir-place', name: 'choose_seat')]
    public function index(PlaceRepository $placeRepository): Response
    {
        // Récupère toutes les places
        $places = $placeRepository->findAll();

        return $this->render('seat/choose_seat.html.twig', [
            'places' => $places,
        ]);
    }

    #[Route('/reserve-place', name: 'reserve_seat', methods: ['POST'])]
    public function reservePlace(Request $request, PlaceRepository $placeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer le code de la place depuis la requête
        $data = json_decode($request->getContent(), true);
        $placeCode = $data['code'] ?? null;

        // Vérifier que le code de la place est présent
        if (!$placeCode) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Code de place manquant.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer la place depuis la base de données
        $place = $placeRepository->findOneBy(['code' => $placeCode]);

        // Vérifier si la place existe
        if (!$place) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Place non trouvée.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si la place est déjà réservée (is_available == false)
        if (!$place->getIsAvailable()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Cette place est déjà réservée.'
            ], Response::HTTP_CONFLICT);
        }

        // Marquer la place comme réservée (is_available = false)
        $place->setIsAvailable(false);

        // Sauvegarder les modifications en base de données via l'EntityManager
        try {
            $entityManager->flush(); // Enregistrer les changements
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la réservation : ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retourner une réponse JSON confirmant la réservation
        return new JsonResponse([
            'success' => true,
            'message' => "La place $placeCode a été réservée avec succès !"
        ]);
    }
}
?>
