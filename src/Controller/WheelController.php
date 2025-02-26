<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PanierService;
use App\Entity\User;

class WheelController extends AbstractController 
{
    #[Route('/wheel', name: 'app_wheel')]
    public function index(SessionInterface $session, PanierService $panierService, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
    
        // Récupération de l'utilisateur à partir de l'ID
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
    
        $panierInfo = $panierService->getPanier();
        $hasPlayed = $session->get('has_played', false); // Vérifie si l'utilisateur a déjà joué
    
        return $this->render('wheel/index.html.twig', [
            'panierTotal'   => $panierInfo['totalPanier'],
            'hasPlayed'     => $hasPlayed, // Envoi à Twig
            'loggedInUser'  => $loggedInUser,
        ]);
    }
    
    #[Route('/wheel/apply-discount', name: 'app_wheel_apply_discount', methods: ['POST'])]
    public function applyDiscount(Request $request, SessionInterface $session): JsonResponse
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        if ($session->get('has_played', false)) {
            return $this->json(['success' => false, 'message' => 'Vous avez déjà joué cette session.'], 400);
        }
    
        $data = json_decode($request->getContent(), true);
        $discountPercentage = $data['discountPercentage'] ?? null;
    
        if ($discountPercentage !== null) {
            $session->set('discount_percentage', $discountPercentage);
            $session->set('has_played', true); // Empêcher de rejouer
    
            return $this->json(['success' => true, 'discountPercentage' => $discountPercentage]);
        }
    
        return $this->json(['success' => false, 'message' => 'Réduction invalide'], 400);
    }
}
