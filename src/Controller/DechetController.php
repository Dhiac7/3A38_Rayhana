<?php

namespace App\Controller;

use App\Entity\Dechet;
use App\Form\DechetType;
use App\Repository\DechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/dechet')]
final class DechetController extends AbstractController
{
    #[Route(name: 'app_dechet_index', methods: ['GET'])]
public function index(Request $request, DechetRepository $dechetRepository, PaginatorInterface $paginator, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
    if ($loggedInUser->getRole() !== 'agriculteur') {
        return $this->redirectToRoute('app_dashboard'); 
    }
    
    // Récupérer la direction du tri et le champ à trier
    $sort = $request->query->get('sort', 'asc'); // 'asc' par défaut
    $field = $request->query->get('field', 'type'); // Tri par type par défaut

    // S'assurer que le tri est 'asc' ou 'desc'
    if (!in_array($sort, ['asc', 'desc'])) {
        $sort = 'asc'; // Valeur par défaut si invalidité
    }

    // Construire la requête pour trier
    $query = $dechetRepository->createQueryBuilder('d')
        ->orderBy('d.' . $field, $sort)
        ->getQuery();

    // Pagination
    $pagination = $paginator->paginate(
        $query, // Requête avec tri
        $request->query->getInt('page', 1), // Page actuelle
        4 // Nombre d'éléments par page
    );

    return $this->render('dechet/indexBack.html.twig', [
        'pagination' => $pagination,
        'loggedInUser' => $loggedInUser,
    ]);
}

#[Route('/new', name: 'app_dechet_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    $loggedInUserId = $session->get('admin_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
    $dechet = new Dechet();
    $form = $this->createForm(DechetType::class, $dechet);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Persister l'objet dans la base de données
        $entityManager->persist($dechet);
        $entityManager->flush();
        // Rediriger vers la liste des déchets
        $this->addFlash('success', "L'atelier a été ajouté avec succès.");
        return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
    }
    // Rendre la vue pour afficher le formulaire
    return $this->render('dechet/new.html.twig', [
        'dechet' => $dechet,
        'form' => $form->createView(), 
        'loggedInUser' => $loggedInUser,
    ]);
} 


    #[Route('/{id}', name: 'app_dechet_show', methods: ['GET'])]
    public function show(Dechet $dechet , SessionInterface $session , EntityManagerInterface $entityManager): Response
    {   $loggedInUserId = $session->get('client_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }
        return $this->render('dechet/show.html.twig', [
            'dechet' => $dechet,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dechet $dechet, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {$loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $form = $this->createForm(DechetType::class, $dechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dechet/edit.html.twig', [
            'dechet' => $dechet,
            'form' => $form,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/dechet/delete/{id}', name: 'app_dechet_delete', methods: ['GET'])]
    public function delete(Request $request, Dechet $dechet, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {$loggedInUserId = $session->get('admin_user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $entityManager->remove($dechet);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_dechet_index', [], Response::HTTP_SEE_OTHER);

    }


    #[Route('/dechet/delete-ajax', name: 'app_dechet_delete_ajax', methods: ['POST'])]
public function deleteAjax(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'])) {
        return new JsonResponse(['success' => false, 'message' => 'ID non fourni'], 400);
    }

    $dechet = $entityManager->getRepository(Dechet::class)->find($data['id']);

    if (!$dechet) {
        return new JsonResponse(['success' => false, 'message' => 'Déchet introuvable'], 404);
    }

    $entityManager->remove($dechet);
    $entityManager->flush();

    return new JsonResponse(['success' => true]);
}

    


#[Route('/dechet/list', name: 'dechet_list_ajax')]
public function listdechet(Request $request, EntityManagerInterface $entityManager): Response
{
    $sortOrder = $request->query->get('sort', '');
    $searchQuery = $request->query->get('search', '');
        $atelierRepository = $entityManager->getRepository(dechet::class);
    $queryBuilder = $atelierRepository->createQueryBuilder('a');

    if (!empty($searchQuery)) {
        $queryBuilder->andWhere('a.type LIKE :search')
                     ->setParameter('search', '%' . $searchQuery . '%');
    }

    if ($sortOrder === "asc") {
        $queryBuilder->orderBy('a.type', 'ASC');
    } elseif ($sortOrder === "desc") {
        $queryBuilder->orderBy('a.type', 'DESC');
    }

    $dechets = $queryBuilder->getQuery()->getResult();

    return $this->render('dechet/_list_dechet.html.twig', [
        'dechets' => $dechets,
    ]);
}

}
