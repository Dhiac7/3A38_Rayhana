<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/atelier')]
final class AtelierController extends AbstractController
{
    #[Route(name: 'app_atelier_index', methods: ['GET'])]
   // public function index(AtelierRepository $atelierRepository): Response
   public function index(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator): Response
    {
        $query = $atelierRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            4 // nbr element par page 
        );
        return $this->render('atelier/index.html.twig', [
            'pagination' => $pagination,
        ]);
        /*return $this->render('atelier/index.html.twig', [
            'ateliers' => $atelierRepository->findAll(),
        ]);*/
    }

    #[Route('/new', name: 'app_atelier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si un atelier existe déjà à la même date
            $existingAtelier = $em->getRepository(Atelier::class)->findOneBy([
                'date_atelier' => $atelier->getDateAtelier()
            ]);
    
            if ($existingAtelier) {
                // Si un atelier existe déjà à cette date, afficher un message d'erreur
                $this->addFlash('error', "Il existe déjà un atelier le " . $atelier->getDateAtelier()->format('Y-m-d H:i:s') . " Avec le nom  : " . $existingAtelier->getNom());
                return $this->redirectToRoute('app_atelier_new'); // Redirige vers la page de création pour afficher l'erreur
            }
    
            // Si pas de conflit de date, persister l'atelier
            $em->persist($atelier);
            $em->flush();
            $this->addFlash('success', "L'atelier a été ajouté avec succès.");
            return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('atelier/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }
    

      
    #[Route('/{id}', name: 'app_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('atelier/show.html.twig', [
            'atelier' => $atelier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_atelier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('atelier/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_atelier_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($atelier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
    }
}
