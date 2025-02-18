<?php

namespace App\Controller;
use App\Entity\User;

use App\Entity\Atelier;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
#[Route('/atelier')]
final class AtelierController extends AbstractController
{
    #[Route(name: 'app_atelier_index', methods: ['GET'])]
   public function index(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator , SessionInterface $session , EntityManagerInterface $entityManager): Response
    { $loggedInUserId = $session->get('client_user_id');
        
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
            6 // nbr element par page 
        );
        return $this->render('atelier/index.html.twig', [
            'pagination' => $pagination,
            'loggedInUser' => $loggedInUser,

        ]);
        
    }
    

   
    #[Route('/{id}', name: 'app_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('atelier/show.html.twig', [
            'atelier' => $atelier,
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
    
        return $this->render('atelier/_list.html.twig', [
            'ateliers' => $ateliers,
        ]);
    }
    
}
