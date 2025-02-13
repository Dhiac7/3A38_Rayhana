<?php

namespace App\Controller;

use App\Entity\Transactionfinancier;
use App\Form\TransactionfinancierType;
use App\Repository\TransactionfinancierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/transactionfinancier')]
final class TransactionfinancierController extends AbstractController{
    #[Route(name: 'app_transactionfinancier_index', methods: ['GET'])]
    public function index(TransactionfinancierRepository $transactionfinancierRepository,SessionInterface $session): Response
    {
        $user = $session->get('user');
        return $this->render('transactionfinancier/index.html.twig', [
            'transactionfinanciers' => $transactionfinancierRepository->findAll(),
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_transactionfinancier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $user = $session->get('user');
        $transactionfinancier = new Transactionfinancier();
        $form = $this->createForm(TransactionfinancierType::class, $transactionfinancier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transactionfinancier);
            $entityManager->flush();

            return $this->redirectToRoute('app_transactionfinancier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transactionfinancier/new.html.twig', [
            'transactionfinancier' => $transactionfinancier,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_transactionfinancier_show', methods: ['GET'])]
    public function show(Transactionfinancier $transactionfinancier): Response
    {
        return $this->render('transactionfinancier/show.html.twig', [
            'transactionfinancier' => $transactionfinancier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transactionfinancier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transactionfinancier $transactionfinancier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionfinancierType::class, $transactionfinancier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transactionfinancier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transactionfinancier/edit.html.twig', [
            'transactionfinancier' => $transactionfinancier,
            'form' => $form,
            
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