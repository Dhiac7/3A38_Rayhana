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
    public function index(ParcelleRepository $parcelleRepository): Response
    {
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env
        return $this->render('parcelle/index.html.twig', [
            'parcelles' => $parcelleRepository->findAll(),
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

    public function saveSatelliteImage(Parcelle $parcelle): string
    {
        $apiKey = 'AlzaSyz-zGr-dfG4zCcSbO9fsk1DplKNExITVcw';
        $latitude = $parcelle->getLatitude();
        $longitude = $parcelle->getLongitude();
        $zoom = 12;
        $size = "800x900";
        $mapType = "satellite";
    
        $imageUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$mapType}&key={$apiKey}";

        $imagePath = 'img/parcelles/parcelle_' . $parcelle->getId() . '.png';
    
        $httpClient = HttpClient::create();
    
        try {
            $response = $httpClient->request('GET', $imageUrl);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false); // Ne pas lever d'exception
            
            if ($statusCode !== 200) {
                throw new \Exception("Erreur API Google Maps : Code $statusCode - Réponse : " . substr($content, 0, 500));
            }
    
            // Vérifier si le contenu est une image
            if (strpos($response->getHeaders()['content-type'][0], 'image') === false) {
                file_put_contents('debug_google_response.html', $content);
                throw new \Exception("L'API Google Maps a retourné un contenu invalide. Vérifie 'debug_google_response.html'.");
            }
    
            $filesystem = new Filesystem();
            $filesystem->dumpFile($imagePath, $response->getContent());
    
            return $imagePath;
        } catch (ClientExceptionInterface | TransportExceptionInterface $e) {
            throw new \Exception("Erreur lors de la récupération de l'image : " . $e->getMessage());
        }
    }

}
