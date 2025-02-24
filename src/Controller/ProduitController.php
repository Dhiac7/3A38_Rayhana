<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\View\TwitterBootstrap5View;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;




#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, Request $request, SessionInterface $session, EntityManagerInterface $entityManager,PaginatorInterface $paginator): Response
    {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        
        // Récupérer tous les produits
        $query = $produitRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            2 // nbr element par page 
        );
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('produit/index.html.twig', [
            /*'produits' => $produits,*/
            'loggedInUser' => $loggedInUser,
            'pagination' => $pagination,
            'MAPBOX_API_KEY' => $mapboxApiKey,
        ]);
    }


    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'loggedInUser' => $loggedInUser,
        ]);
    }
}