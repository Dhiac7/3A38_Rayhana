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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, Request $request, SessionInterface $session, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        
        // Get selected category from request
        $category = $request->query->get('category');
        
        // Get products by category (or all if no category is selected)
        $query = $produitRepository->findByCategory($category);
        
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            4
        );
        
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY'];
    
        return $this->render('produit/index.html.twig', [
            'loggedInUser' => $loggedInUser,
            'pagination' => $pagination,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'currentCategory' => $category,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, SessionInterface $session, EntityManagerInterface $entityManager, CacheManager $cacheManager): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
    
        // Generate filtered image path for 'thumbnail' filter
        $imagePath = $cacheManager->getBrowserPath('img/stock/' . $produit->getImage(), 'thumbnail');
        
        // Generate original image path for backup
        $originalImagePath = $this->getParameter('kernel.project_dir') . '/public/img/stock/' . $produit->getImage();
        $imageExists = file_exists($originalImagePath);
        
        // Get related products (you may want to implement this method in your repository)
        // Example: $relatedProducts = $produitRepository->findRelatedProducts($produit->getId(), $produit->getCategory(), 4);
        
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'loggedInUser' => $loggedInUser,
            'imagePath' => $imagePath,
            'imageExists' => $imageExists,
            // 'relatedProducts' => $relatedProducts, // Uncomment if you implement this
        ]);
    }
}