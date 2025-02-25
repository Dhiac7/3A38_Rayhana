<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    private PanierService $panierService;
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(PanierService $panierService, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->panierService = $panierService;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    private function getLoggedInUser(): ?User
    {
        $session = $this->requestStack->getSession();
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return null;
        }

        return $this->entityManager->getRepository(User::class)->find($loggedInUserId);
    }

    #[Route('/panier', name: 'panier_index')]
    public function index(): Response
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $panier = $this->panierService->getPanier();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier['panierData'],
            'totalPanier' => $panier['totalPanier'],
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(int $id): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $this->panierService->ajouterProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/ajouter1/{id}', name: 'panier_ajouter1')]
    public function ajouter1(int $id): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $this->panierService->ajouterProduit($id);
        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/panier/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(int $id): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $this->panierService->supprimerProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/diminuer/{id}', name: 'panier_diminuer')]
    public function diminuer(int $id): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $this->panierService->diminuerProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/vider', name: 'panier_vider')]
    public function vider(): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $this->panierService->viderPanier();
        return $this->redirectToRoute('panier_index');
    }
}
