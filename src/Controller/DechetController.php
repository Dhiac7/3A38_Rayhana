<?php

namespace App\Controller;

use App\Entity\Dechet;
use App\Form\DechetType;
use App\Repository\DechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/dechet')]
final class DechetController extends AbstractController
{
    #[Route(name: 'app_dechet_index', methods: ['GET'])]
    //public function index(DechetRepository $dechetRepository): Response
    public function index(Request $request, DechetRepository $dechetRepository, PaginatorInterface $paginator , SessionInterface $session , EntityManagerInterface $entityManager): Response
    {   $loggedInUserId = $session->get('client_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        $query = $dechetRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            4 // nbr element par page 
        );
        return $this->render('dechet/indexBack.html.twig', [
            'pagination' => $pagination,
            'loggedInUser' => $loggedInUser,

        ]);

        
        /*return $this->render('dechet/index.html.twig', [
            'ateliers' => $atelierRepository->findAll(),
        ]);*/
    }

    #[Route('/new', name: 'app_dechet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , SessionInterface $session ): Response
    {$loggedInUserId = $session->get('client_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        $dechet = new Dechet();
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dechet);
            $entityManager->flush();

            return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dechet/new.html.twig', [
            'dechet' => $dechet,
            'form' => $form,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/{id}', name: 'app_dechet_show', methods: ['GET'])]
    public function show(Dechet $dechet , SessionInterface $session , EntityManagerInterface $entityManager): Response
    {   $loggedInUserId = $session->get('client_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        return $this->render('dechet/show.html.twig', [
            'dechet' => $dechet,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dechet $dechet, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {$loggedInUserId = $session->get('client_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dechet/edit.html.twig', [
            'dechet' => $dechet,
            'form' => $form,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/{id}', name: 'app_dechet_delete', methods: ['POST'])]
    public function delete(Request $request, Dechet $dechet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dechet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dechet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/dechet/list', name: 'dechet_list_ajax')]
    public function listAteliers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortOrder = $request->query->get('sort', '');
        $searchQuery = $request->query->get('search', '');
            $dechetRepository = $entityManager->getRepository(dechet::class);
        $queryBuilder = $dechetRepository->createQueryBuilder('a');
    
        if (!empty($searchQuery)) {
            $queryBuilder->andWhere('a.nom LIKE :search')
                         ->setParameter('search', '%' . $searchQuery . '%');
        }
    
        if ($sortOrder === "asc") {
            $queryBuilder->orderBy('a.nom', 'ASC');
        } elseif ($sortOrder === "desc") {
            $queryBuilder->orderBy('a.nom', 'DESC');
        }
    
        $dechet = $queryBuilder->getQuery()->getResult();
    
        return $this->render('dechet/_list.html.twig', [
            'dechet' => $dechet,
        ]);
    }
    
}
