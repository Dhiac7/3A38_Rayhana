<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    private PanierService $panierService;

    public function __construct(PanierService $panierService)
    {
        $this->panierService = $panierService;
    }

   
    #[Route('/panier', name: 'panier_index')]
    public function index(): Response
    {
        $panier = $this->panierService->getPanier();
    
        return $this->render('panier/index.html.twig', [
            'panier' => $panier['panierData'],
            'totalPanier' => $panier['totalPanier'],
        ]);
    }

    
    #[Route('/panier/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(int $id): RedirectResponse
    {
        $this->panierService->ajouterProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/ajouter1/{id}', name: 'panier_ajouter1')]
    public function ajouter1(int $id): RedirectResponse
    {
        $this->panierService->ajouterProduit($id);
        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/panier/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(int $id): RedirectResponse
    {
        $this->panierService->supprimerProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/diminuer/{id}', name: 'panier_diminuer')]
    public function diminuer(int $id): RedirectResponse
    {
        $this->panierService->diminuerProduit($id);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/panier/vider', name: 'panier_vider')]
    public function vider(): RedirectResponse
    {
        $this->panierService->viderPanier();
        return $this->redirectToRoute('panier_index');
    }
}
