<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AtelierLike;
use App\Entity\Atelier;
use App\Entity\AtelierLikes;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RedirectRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




#[Route('/atelier')]
final class AtelierController extends AbstractController
{
    #[Route(name: 'app_atelier_index', methods: ['GET'])]
   public function index(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator , SessionInterface $session , EntityManagerInterface $entityManager): Response
    { $loggedInUserId = $session->get('user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        
        $query = $atelierRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            4 // nbr element par page 
        );
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('atelier/index.html.twig', [
            'pagination' => $pagination, // La pagination est bien passée
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'loggedInUser' => $loggedInUser,
        ]);
        
    }
    

   
    #[Route('/{id}', name: 'app_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('atelier/show.html.twig', [
            'atelier' => $atelier,
            'MAPBOX_API_KEY' => $mapboxApiKey,

        ]);
    }

   

    #[Route('/{id}', name: 'app_atelier_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($atelier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/atelier/list', name: 'atelier_list_ajax')]
    public function listAteliers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortOrder = $request->query->get('sort', '');
        $searchQuery = $request->query->get('search', '');
            $atelierRepository = $entityManager->getRepository(Atelier::class);
        $queryBuilder = $atelierRepository->createQueryBuilder('a');
    
        if (!empty($searchQuery)) {
            $queryBuilder->andWhere('a.nom LIKE :search')
                         ->setParameter('search', '%' . $searchQuery . '%');
        }
    
        if ($sortOrder === "asc") {
            $queryBuilder->orderBy('a.nom', 'ASC');
        } elseif ($sortOrder === "desc") {
            $queryBuilder->orderBy('a.nom', 'DESC');
        }
    
        $ateliers = $queryBuilder->getQuery()->getResult();
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('atelier/_list.html.twig', [
            'ateliers' => $ateliers,
            'MAPBOX_API_KEY' => $mapboxApiKey,

        ]);
    }


    #[Route('/{id}/like', name: 'app_atelier_like', methods: ['POST'])]
    public function likeAtelier($id, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): RedirectResponse
    {
        // Récupérer l'atelier
        $atelier = $entityManager->getRepository(Atelier::class)->find($id);
        if (!$atelier) {
            throw $this->createNotFoundException('Atelier not found');
        }
    
        // Récupérer l'utilisateur connecté
        $loggedInUserId = $session->get('user_id');
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Déterminer si c'est un like ou un dislike
        $isLike = $request->request->get('like') === 'true';
    
        // Vérifier si l'utilisateur a déjà liké/disliké cet atelier
        $atelierLikeRepo = $entityManager->getRepository(AtelierLikes::class);
        $existingLike = $atelierLikeRepo->findOneBy(['atelier' => $atelier, 'user' => $loggedInUser]);
    
        if ($existingLike) {
            // Si l'utilisateur clique à nouveau sur le même bouton, supprimer le like/dislike
            if ($existingLike->isLiked() === $isLike) {
                $entityManager->remove($existingLike);
            } else {
                // Sinon, mettre à jour le like/dislike
                $existingLike->setIsLiked($isLike);
                $entityManager->persist($existingLike);
            }
        } else {
            // Créer un nouveau like/dislike
            $newLike = new AtelierLikes();
            $newLike->setAtelier($atelier);
            $newLike->setUser($loggedInUser);
            $newLike->setIsLiked($isLike);
            $entityManager->persist($newLike);
        }
    
        // Sauvegarder les modifications
        $entityManager->flush();
    
        // Rediriger vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    
}
