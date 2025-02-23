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
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\View\TwitterBootstrap5View;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, Request $request): Response
    {
        // Créez une requête pour récupérer tous les produits
        $queryBuilder = $produitRepository->createQueryBuilder('p');

        // Créez une instance de Pagerfanta avec l'adaptateur Doctrine ORM
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        // Définissez le nombre maximum d'éléments par page
        $pagerfanta->setMaxPerPage(2);

        // Définissez la page courante à partir de la requête
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        // Créez une vue pour la pagination (optionnel, pour personnaliser l'affichage des liens de pagination)
        $view = new TwitterBootstrap5View();
        $options = [];
        $routeGenerator = function ($page) {
            return $this->generateUrl('app_produit_index', ['page' => $page]);
        };
        $paginationHtml = $view->render($pagerfanta, $routeGenerator, $options);

        return $this->render('produit/index.html.twig', [
            'produits' => $pagerfanta->getCurrentPageResults(),
            'pager' => $pagerfanta,
            'pagination' => $paginationHtml,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}