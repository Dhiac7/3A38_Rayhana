<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use App\Entity\User;

#[Route('/stock')]
final class StockController extends AbstractController
{
    #[Route(name: 'app_stock_index', methods: ['GET'])]
    public function index(StockRepository $stockRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('stock/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, StockRepository $stockRepository): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        // Vérification de l'unicité du nom du stock
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
            $existingStock = $stockRepository->findOneBy(['nom' => $nom]);

            if ($existingStock) {
                $form->get('nom')->addError(new FormError('Ce nom de stock existe déjà.'));
            }

            if (!$form->getErrors()->count()) { // Si aucune erreur n'a été ajoutée
                $entityManager->persist($stock);
                $entityManager->flush();

                return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $entityManager, SessionInterface $session, StockRepository $stockRepository): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        // Vérification de l'unicité du nom du stock
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
            $existingStock = $stockRepository->findOneBy(['nom' => $nom]);

            if ($existingStock && $existingStock !== $stock) {  // Vérifie si c'est le même stock
                $form->get('nom')->addError(new FormError('Ce nom de stock existe déjà.'));
            }

            if (!$form->getErrors()->count()) { // Si aucune erreur n'a été ajoutée
                $entityManager->flush();

                return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        if ($this->isCsrfTokenValid('delete'.$stock->getId(), $request->get('_token'))) {  // Correction du token
            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }
}
