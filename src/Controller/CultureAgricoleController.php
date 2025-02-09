<?php

namespace App\Controller;

use App\Entity\CultureAgricole;
use App\Form\CultureAgricoleType;
use App\Repository\CultureAgricoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/culture/agricole')]
final class CultureAgricoleController extends AbstractController
{
    #[Route(name: 'app_culture_agricole_index', methods: ['GET'])]
    public function index(Request $request, CultureAgricoleRepository $cultureAgricoleRepository, PaginatorInterface $paginator): Response
    {
            $query = $cultureAgricoleRepository->findAll();
            $pagination = $paginator->paginate(
            $query, // Donneili bch namlou pagination
            $request->query->getInt('page', 1), // Num page 
            4 // nbr element par page 
        );
        return $this->render('culture_agricole/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_culture_agricole_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cultureAgricole = new CultureAgricole();
        $form = $this->createForm(CultureAgricoleType::class, $cultureAgricole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cultureAgricole);
            $entityManager->flush();

            return $this->redirectToRoute('app_culture_agricole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('culture_agricole/new.html.twig', [
            'culture_agricole' => $cultureAgricole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_culture_agricole_show', methods: ['GET'])]
    public function show(CultureAgricole $cultureAgricole): Response
    {
        return $this->render('culture_agricole/show.html.twig', [
            'culture_agricole' => $cultureAgricole,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_culture_agricole_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CultureAgricole $cultureAgricole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CultureAgricoleType::class, $cultureAgricole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_culture_agricole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('culture_agricole/edit.html.twig', [
            'culture_agricole' => $cultureAgricole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_culture_agricole_delete', methods: ['POST'])]
    public function delete(Request $request, CultureAgricole $cultureAgricole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cultureAgricole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cultureAgricole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_culture_agricole_index', [], Response::HTTP_SEE_OTHER);
    }
}
