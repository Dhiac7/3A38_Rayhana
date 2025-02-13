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

#[Route('/parcelle')]
final class ParcelleController extends AbstractController
{
    #[Route(name: 'app_parcelle_index', methods: ['GET'])]
    public function index(Request $request, ParcelleRepository $parcelleRepository): Response
    {
        $page = (int) $request->query->get('page', 1); // Get the current page from the query parameters
        $limit = 9; // Number of parcelles per page
        $offset = ($page - 1) * $limit;
    
        // Get the parcelles for the current page
        $parcelles = $parcelleRepository->findBy([], [], $limit, $offset);
    
        // Get the total number of parcelles for pagination
        $totalParcelles = $parcelleRepository->count([]);
    
        // Calculate total pages
        $totalPages = ceil($totalParcelles / $limit);
    
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env
        return $this->render('parcelle/index.html.twig', [
            'parcelles' => $parcelles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'MAPBOX_API_KEY' => $mapboxApiKey, // Pass to Twig
        ]);
    }
    

    #[Route('/new', name: 'app_parcelle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parcelle = new Parcelle();
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parcelle);
            $entityManager->flush();
            //$this->saveSatelliteImage($parcelle);

            return $this->redirectToRoute('app_parcelle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/new.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parcelle_show', methods: ['GET'])]
    public function show(Parcelle $parcelle): Response
    {
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('parcelle/show.html.twig', [
            'parcelle' => $parcelle,
            'MAPBOX_API_KEY' => $mapboxApiKey,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parcelle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parcelle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/edit.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parcelle_delete', methods: ['POST'])]
    public function delete(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parcelle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parcelle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parcelle_index', [], Response::HTTP_SEE_OTHER);
    }


    //ADMIN
    #[Route('/dashboard', name: 'app_parcelle_index2', methods: ['GET'])]
    public function dashparcelle(Request $request, ParcelleRepository $parcelleRepository): Response
    {
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
        ]);
    }
    
    

    

    

    #[Route('/dashboard/new', name: 'app_parcelle_new2', methods: ['GET', 'POST'])]
    public function new2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parcelle = new Parcelle();
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parcelle);
            $entityManager->flush();
            //$this->saveSatelliteImage($parcelle);

            return $this->redirectToRoute('app_parcelle_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/newAdmin.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_parcelle_show2', methods: ['GET'])]
    public function show2(Parcelle $parcelle): Response
    {
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('parcelle/showAdmin.html.twig', [
            'parcelle' => $parcelle,
            'MAPBOX_API_KEY' => $mapboxApiKey,
        ]);
    }

    #[Route('/dashboard/{id}/edit', name: 'app_parcelle_edit2', methods: ['GET', 'POST'])]
    public function edit2(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParcelleType::class, $parcelle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parcelle_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parcelle/editAdmin.html.twig', [
            'parcelle' => $parcelle,
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_parcelle_delete2', methods: ['POST'])]
    public function delete2(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parcelle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parcelle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parcelle_index2', [], Response::HTTP_SEE_OTHER);
    }


    

}
