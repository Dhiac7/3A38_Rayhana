<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/avis')]
final class AvisController extends AbstractController{
    #[Route(name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository, SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('client_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }

    // Filtrer les ventes pour l'utilisateur connecté
    $query = $avisRepository->createQueryBuilder('v')
        ->where('v.client = :client')
        ->setParameter('client', $loggedInUser)
        ->getQuery();


        return $this->render('avis/index.html.twig', [
            'avis' => $query->getResult(), // Utilisation du résultat de la requête filtrée
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('client_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avi->setClient($loggedInUser);

            $entityManager->persist($avi);
            $entityManager->flush();
            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'], requirements: ['id' => '\d+'])]
public function show(Avis $avi, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    $loggedInUserId = $session->get('client_user_id');
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('gi');
    }
    return $this->render('avis/show.html.twig', [
        'avi' => $avi,
        'loggedInUser' => $loggedInUser,
    ]);
}


    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager,SessionInterface $session ): Response
    {
        $loggedInUserId = $session->get('client_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avi,SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($avi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }


    /////////////////////////////////////////////// SHOW AVIS fel backoffice ///////////////////////////////////////////////


    #[Route('/avisback', name: 'app_avisback_index', methods: ['GET'])]
public function index2(AvisRepository $avisRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    $loggedInUserId = $session->get('admin_user_id');
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
    return $this->render('avis/indexback.html.twig', [
        'avis' => $avisRepository->findAll(),
        'loggedInUser' => $loggedInUser,
    ]);
}



    
}
