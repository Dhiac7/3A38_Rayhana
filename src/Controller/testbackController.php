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
#[Route('/testBack')]
final class testbackController extends AbstractController
{
    #[Route(name: 'app_testBack_index2', methods: ['GET'])]
   // public function index2(AtelierRepository $atelierRepository): Response
   public function index2(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator): Response
    {
        $query = $atelierRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            6 // nbr element par page 
        );
        return $this->render('testBack/index2.html.twig', [
            'pagination' => $pagination,
        ]);
        /*return $this->render('testBack/index2.html.twig', [
            'ateliers' => $atelierRepository->findAll(),
        ]);*/
    }

    #[Route('/new', name: 'app_testBack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // Vérifie si un atelier existe déjà à la même date
           $existingAtelier = $em->getRepository(Atelier::class)->findOneBy([
                'date_atelier' => $atelier->getDateAtelier()
            ]);
    
            if ($existingAtelier) {
                $this->addFlash('error', "Il existe déjà un atelier le " . $atelier->getDateAtelier()->format('Y-m-d') . " avec le nom : " . $existingAtelier->getNom());
                return $this->redirectToRoute('app_testBack_new');
            }
            
            else {
            // Si pas de conflit de date, persister l'atelier
            $em->persist($atelier);
            $em->flush();
            $this->addFlash('success', "L'atelier a été ajouté avec succès.");
            return $this->redirectToRoute('app_testBack_index2', [], Response::HTTP_SEE_OTHER);
            }
        }
    
        return $this->render('testBack/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }
    

      
    #[Route('/{id}', name: 'app_testBack_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('testBack/show.html.twig', [
            'atelier' => $atelier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_testBack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_testBack_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('testBack/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_testBack_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($atelier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_testBack_index2', [], Response::HTTP_SEE_OTHER);
    }
}
