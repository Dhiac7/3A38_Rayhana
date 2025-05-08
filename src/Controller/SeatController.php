<?php

namespace App\Controller;

use App\Repository\PlaceRepository;
use App\Repository\AtelierRepository;
use App\Entity\Atelier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class SeatController extends AbstractController
{#[Route('/choisir-place', name: 'choose_seat')]
    public function index(Request $request, EntityManagerInterface $entityManager,PlaceRepository $placeRepository,SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('user_id');
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $places = $placeRepository->findAll();
        $nom = $request->query->get('nom');
        $prix = $request->query->get('prix');
        $dateAtelier = $request->query->get('dateAtelier');
    
        // Récupérer l'atelier depuis la base de données
        $atelier = $entityManager->getRepository(Atelier::class)->findOneBy([
            'nom' => $nom,
            'prix' => $prix,
            'date_atelier' => new \DateTime($dateAtelier) // Assurez-vous que le champ est correct
        ]);
    
        if (!$atelier) {
            // Gérer le cas où l'atelier n'existe pas
            $this->addFlash('error', 'Atelier non trouvé.');
            return $this->redirectToRoute('app_vente_index');
        }
        return $this->render('seat/choose_seat.html.twig', [
            'places' => $places,
            'atelier' => $atelier,
            'loggedInUser' => $loggedInUser,
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
    //$place->setAtelier($atelier); // Utilisez setIsAvailable(false) pour désactiver la place
    $entityManager->flush();


    return new JsonResponse(['success' => true, 'message' => "La place $placeCode a été réservée avec succès !"]);
}
}    
?>
