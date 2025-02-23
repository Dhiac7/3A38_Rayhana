<?php

namespace App\Controller;

use App\Entity\Inspection;
use App\Entity\Avis;
use App\Repository\AvisRepository;

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



    #[Route('/{id}/repondre', name: 'app_inspection_repondre', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
public function repondreToAvis(
    int $id, 
    Request $request, 
    AvisRepository $avisRepository, 
    EntityManagerInterface $entityManager, 
    SessionInterface $session
): Response {
    // Vérifier si l'utilisateur admin est connecté
    $loggedInUserId = $session->get('admin_user_id');
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }

    // Récupérer l'avis auquel on veut répondre
    $avis = $avisRepository->find($id);
    if (!$avis) {
        throw $this->createNotFoundException("Avis non trouvé.");
    }

    // Créer une nouvelle inspection et l'associer à l'avis
    $inspection = new Inspection();
    $inspection->setAvis($avis);

    // Créer le formulaire pour l'inspection (adapté selon InspectionType)
    $form = $this->createForm(InspectionType::class, $inspection);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($inspection);
        $entityManager->flush();

        // Rediriger vers une page de détails de l'avis, ou vers la liste des inspections
        return $this->redirectToRoute('app_avisback_index', ['id' => $avis->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->render('inspection/new.html.twig', [
        'inspection'   => $inspection,
        'form'         => $form->createView(),
        'loggedInUser' => $loggedInUser,
    ]);
}

#[Route('/avis/{id}/list', name: 'app_inspection_list_for_avis', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function listForAvis(
        Avis $avis, 
        InspectionRepository $inspectionRepository, 
        SessionInterface $session, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'administrateur est connecté
        $loggedInUserId = $session->get('admin_user_id');
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        // Récupérer les inspections associées à cet avis
        $inspections = $inspectionRepository->findBy(['avis' => $avis]);

        return $this->render('inspection/list_for_avis.html.twig', [
            'avis' => $avis,
            'inspections' => $inspections,
            'loggedInUser' => $loggedInUser,
        ]);
    }




}
