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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CodePromoRepository;

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
    public function index(SessionInterface $session): Response
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        $panier = $this->panierService->getPanier();
        $discountPercentage = $session->get('discount_percentage', 0);
        $codePromoPercentage = $session->get('code_promo_percentage', 0);
        $codePromoCode = $session->get('code_promo_code');

        // Calculer le prix après réduction de la roulette
        $totalApreReductionRoulette = $panier['totalPanier'];
        $montantReductionRoulette = 0;

        if ($discountPercentage > 0) {
            $montantReductionRoulette = $panier['totalPanier'] * $discountPercentage / 100;
            $totalApreReductionRoulette = $panier['totalPanier'] - $montantReductionRoulette;
        }

        // Calculer le prix après application du code promo
        $totalFinal = $totalApreReductionRoulette;
        $montantReductionCodePromo = 0;
        $totalApresCodePromo = $totalApreReductionRoulette;

        if ($codePromoPercentage > 0) {
            $montantReductionCodePromo = $totalApreReductionRoulette * $codePromoPercentage / 100;
            $totalFinal = $totalApreReductionRoulette - $montantReductionCodePromo;
            $totalApresCodePromo = $totalFinal;
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier['panierData'],
            'totalPanier' => $panier['totalPanier'],
            'loggedInUser' => $loggedInUser,
            'discountPercentage' => $discountPercentage,
            'montantReductionRoulette' => $montantReductionRoulette,
            'totalApreReductionRoulette' => $totalApreReductionRoulette,
            'codePromoPercentage' => $codePromoPercentage,
            'codePromoCode' => $codePromoCode,
            'montantReductionCodePromo' => $montantReductionCodePromo,
            'totalApresCodePromo' => $totalApresCodePromo,
            'totalFinal' => $totalFinal,
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

    #[Route('/panier/appliquer-code', name: 'panier_appliquer_code', methods: ['POST'])]
    public function appliquerCodePromo(Request $request, CodePromoRepository $codePromoRepository, SessionInterface $session): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
    
        $code = $request->request->get('code');
    
        if (!$code) {
            $this->addFlash('danger', 'Veuillez entrer un code promo');
            return $this->redirectToRoute('panier_index');
        }
    
        // Récupération du panier
        $panier = $this->panierService->getPanier();
        $totalPanier = $panier['totalPanier'];
    
        // Vérification du montant minimum
        if ($totalPanier < 100) {
            $this->addFlash('danger', 'Le total du panier doit être au moins de 100 DT pour appliquer un code promo.');
            return $this->redirectToRoute('panier_index');
        }
    
        // Recherche du code promo dans la base de données
        $codePromo = $codePromoRepository->findOneBy(['code' => $code, 'actif' => true]);
    
        if (!$codePromo) {
            $this->addFlash('danger', 'Code promo invalide ou expiré');
            return $this->redirectToRoute('panier_index');
        }
    
        // Vérifier si le code est déjà utilisé
        if ($session->has('code_promo_id') && $session->get('code_promo_id') === $codePromo->getId()) {
            $this->addFlash('info', 'Ce code promo est déjà appliqué');
            return $this->redirectToRoute('panier_index');
        }
    
        // Stocker le code promo et sa réduction dans la session
        $session->set('code_promo_id', $codePromo->getId());
        $session->set('code_promo_percentage', $codePromo->getReduction());
        $session->set('code_promo_code', $codePromo->getCode());
    
        $this->addFlash('success', 'Code promo appliqué avec succès');
        return $this->redirectToRoute('panier_index');
    }
    

    #[Route('/panier/supprimer-code', name: 'panier_supprimer_code')]
    public function supprimerCodePromo(SessionInterface $session): RedirectResponse
    {
        $loggedInUser = $this->getLoggedInUser();
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        // Supprimer le code promo de la session
        $session->remove('code_promo_id');
        $session->remove('code_promo_percentage');
        $session->remove('code_promo_code');

        $this->addFlash('info', 'Code promo supprimé');
        return $this->redirectToRoute('panier_index');
    }
}