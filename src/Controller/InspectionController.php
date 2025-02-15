<?php

namespace App\Controller;

use App\Entity\Inspection;
use App\Entity\User;
use App\Form\InspectionType;
use App\Repository\InspectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/inspection')]
final class InspectionController extends AbstractController{
    #[Route(name: 'app_inspection_index', methods: ['GET'])]
    public function index(InspectionRepository $inspectionRepository, SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
        return $this->render('inspection/index.html.twig', [
            'inspections' => $inspectionRepository->findAll(),
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/new', name: 'app_inspection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
        $inspection = new Inspection();
        $form = $this->createForm(InspectionType::class, $inspection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inspection);
            $entityManager->flush();

            return $this->redirectToRoute('app_inspection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inspection/new.html.twig', [
            'inspection' => $inspection,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_inspection_show', methods: ['GET'])]
    public function show(Inspection $inspection, SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
        return $this->render('inspection/show.html.twig', [
            'inspection' => $inspection,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inspection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inspection $inspection, SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
        $form = $this->createForm(InspectionType::class, $inspection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_inspection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inspection/edit.html.twig', [
            'inspection' => $inspection,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_inspection_delete', methods: ['POST'])]
    public function delete(Request $request, Inspection $inspection, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inspection->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inspection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inspection_index', [], Response::HTTP_SEE_OTHER);
    }
}
