<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Transactionfinancier;
use App\Repository\TransactionfinancierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/transactionfinancier')]
final class TransactionfinancierController extends AbstractController
{
    #[Route(name: 'app_transactionfinancier_index', methods: ['GET'])]
    public function index(
        TransactionfinancierRepository $transactionfinancierRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $query = $transactionfinancierRepository->findAll();
        
        // Pagination
        $transactions = $paginator->paginate(
            $query, // Requête Doctrine
            $request->query->getInt('page', 1), // Page actuelle, par défaut 1
            5 // Nombre d'éléments par page
        );

        return $this->render('transactionfinancier/index.html.twig', [
            'transactionfinanciers' => $transactions,
            'loggedInUser' => $loggedInUser,
        ]);
    }


    #[Route('/new', name: 'app_transactionfinancier_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
{
    $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    $transactionfinancier = new Transactionfinancier();
    $form = $this->createForm(TransactionfinancierType::class, $transactionfinancier);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Set the date manually
        $transactionfinancier->setDate(new \DateTime());

        $entityManager->persist($transactionfinancier);
        $entityManager->flush();

        return $this->redirectToRoute('app_transactionfinancier_index');
    }

    return $this->render('transactionfinancier/new.html.twig', [
        'form' => $form->createView(),
        'loggedInUser' => $loggedInUser,
    ]);
}

    #[Route('/{id}', name: 'app_transactionfinancier_show', methods: ['GET'])]
    public function show(Transactionfinancier $transactionfinancier,SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        return $this->render('transactionfinancier/show.html.twig', [
            'transactionfinancier' => $transactionfinancier,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transactionfinancier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transactionfinancier $transactionfinancier, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $form = $this->createForm(TransactionfinancierType::class, $transactionfinancier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transactionfinancier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transactionfinancier/edit.html.twig', [
            'transactionfinancier' => $transactionfinancier,
            'form' => $form,
           'loggedInUser' => $loggedInUser,
            
        ]);
    }

    #[Route('/{id}', name: 'app_transactionfinancier_delete', methods: ['POST'])]
    public function delete(Request $request, Transactionfinancier $transactionfinancier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transactionfinancier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transactionfinancier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transactionfinancier_index', [], Response::HTTP_SEE_OTHER);
    }
}