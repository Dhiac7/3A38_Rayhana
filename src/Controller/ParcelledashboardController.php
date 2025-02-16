<?php

namespace App\Controller;

use App\Entity\Parcelle;
use App\Form\ParcelleType;
use App\Repository\ParcelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;


final class ParcelledashboardController extends AbstractController
{
    #[Route('/parcelledashboard', name: 'app_parcelle_dashboard_index')]
    public function index(Request $request, ParcelleRepository $parcelleRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $query = $request->query->get('q', '');
        $parcelles = !empty($query) ? $parcelleRepository->searchByName($query) : $parcelleRepository->findAll();
        
        // Si c'est une requête AJAX (XMLHttpRequest)
        if ($request->isXmlHttpRequest()) {
            // Vérifier si les parcelles sont trouvées
            if (empty($parcelles)) {
                return $this->json([]);
            }
    
            return $this->json(array_map(function ($parcelle) {
                return [
                    'id' => $parcelle->getId(),
                    'nom' => $parcelle->getNom(),
                    'irrigationDisponible' => $parcelle->isIrrigationDisponible(),
                    'superficie' => $parcelle->getSuperficie(),
                    'latitude' => $parcelle->getLatitude(),
                    'longitude' => $parcelle->getLongitude(),
                ];
            }, $parcelles));
        }
    
        // Si ce n'est pas une requête AJAX, on rend la vue
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY'];
        
        return $this->render('parcelle/indexAdmin.html.twig', [
            'parcelles' => $parcelles,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'query' => $query,
            'loggedInUser' => $loggedInUser,
        ]);
    }
    
    #[Route('/dashboard', name: 'app_parcelle_index2')]
    public function dashparcelle(Request $request, ParcelleRepository $parcelleRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }


        $query = $request->query->get('q', '');
        $parcelles = !empty($query) ? $parcelleRepository->searchByName($query) : $parcelleRepository->findAll();
        
        // Si c'est une requête AJAX (XMLHttpRequest)
        if ($request->isXmlHttpRequest()) {
            // Vérifier si les parcelles sont trouvées
            if (empty($parcelles)) {
                return $this->json([]);
            }
    
            return $this->json(array_map(function ($parcelle) {
                return [
                    'id' => $parcelle->getId(),
                    'nom' => $parcelle->getNom(),
                    'irrigationDisponible' => $parcelle->isIrrigationDisponible(),
                    'superficie' => $parcelle->getSuperficie(),
                    'latitude' => $parcelle->getLatitude(),
                    'longitude' => $parcelle->getLongitude(),
                ];
            }, $parcelles));
        }
    
        // Si ce n'est pas une requête AJAX, on rend la vue
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY'];
        
        return $this->render('parcelle/indexAdmin.html.twig', [
            'parcelles' => $parcelles,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'query' => $query,
            'loggedInUser' => $loggedInUser,        
        ]);
    }
    

    #[Route('/dashboard/new', name: 'app_parcelle_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $parcelle = new Parcelle();
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parcelle);
            $entityManager->flush();
            //$this->saveSatelliteImage($parcelle);

            return $this->redirectToRoute('app_parcelle_dashboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/newAdmin.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_parcelle_show2', methods: ['GET'])]
    public function show2(Parcelle $parcelle, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('parcelle/showAdmin.html.twig', [
            'parcelle' => $parcelle,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/dashboard/{id}/edit', name: 'app_parcelle_edit2', methods: ['GET', 'POST'])]
    public function edit2(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('admin_user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parcelle_dashboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/editAdmin.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_parcelle_delete2', methods: ['POST'])]
    public function delete2(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parcelle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parcelle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parcelle_dashboard_index', [], Response::HTTP_SEE_OTHER);
    }
}
