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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/culturedash')]
final class CultureAgricoleDashboardController extends AbstractController
{
    #[Route(name: 'app_culture_agricole_dashboard_index', methods: ['GET'])]
    public function index(Request $request, CultureAgricoleRepository $cultureAgricoleRepository, SessionInterface $session): Response
    {
        $user = $session->get('user');

        $limit = 2; // Nombre d'éléments par page
        $page = $request->query->getInt('page', 1); // Page actuelle (par défaut la page 1)
        $offset = ($page - 1) * $limit; // Calculer l'offset

        $totalCultures = $cultureAgricoleRepository->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->getQuery()
        ->getSingleScalarResult();
    

        // Calculer le nombre total de pages
        $totalPages = ceil($totalCultures / $limit);

        return $this->render('culture_agricole_dashboard/index.html.twig', [
            'cultures' => $cultureAgricoleRepository->findAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'user' => $user,

        ]);
    }

    #[Route('/new', name: 'app_culture_agricole_dashboard_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $session->get('user');

        $cultureAgricole = new CultureAgricole();
        $form = $this->createForm(CultureAgricoleType::class, $cultureAgricole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cultureAgricole);
            $entityManager->flush();

            return $this->redirectToRoute('app_culture_agricole_dashboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('culture_agricole_dashboard/new.html.twig', [
            'culture_agricole' => $cultureAgricole,
            'form' => $form,
            'user' => $user,

        ]);
    }

    #[Route('/{id}', name: 'app_culture_agricole_dashboard_show', methods: ['GET'])]
    public function show(CultureAgricole $cultureAgricole, SessionInterface $session): Response
    {
        $user = $session->get('user');

        return $this->render('culture_agricole_dashboard/show.html.twig', [
            'culture_agricole' => $cultureAgricole,
            'user' => $user,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_culture_agricole_dashboard_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CultureAgricole $cultureAgricole, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $session->get('user');

        $form = $this->createForm(CultureAgricoleType::class, $cultureAgricole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_culture_agricole_dashboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('culture_agricole_dashboard/edit.html.twig', [
            'culture_agricole' => $cultureAgricole,
            'form' => $form,
            'user' => $user,

        ]);
    }

    #[Route('/{id}', name: 'app_culture_agricole_dashboard_delete', methods: ['POST'])]
    public function delete(Request $request, CultureAgricole $cultureAgricole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cultureAgricole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cultureAgricole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_culture_agricole_index', [], Response::HTTP_SEE_OTHER);
    }
}
