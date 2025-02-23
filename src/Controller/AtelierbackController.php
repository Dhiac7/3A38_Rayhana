<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Atelier;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/atelierBack')]
final class AtelierbackController extends AbstractController
{

    #[Route(name: 'app_atelier_indexBack', methods: ['GET'])]
    // public function index(AtelierRepository $atelierRepository): Response
    public function indexBack(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator , SessionInterface $session,EntityManagerInterface $entityManager): Response
     {
        $loggedInUserId = $session->get('user_id');
        
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
         $query = $atelierRepository->findAll();
         $pagination = $paginator->paginate(
             $query, // Donneili bch namlou pagination
             $request->query->getInt('page', 1), // Num page 
             6 // nbr element par page 
         );
         $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

         return $this->render('atelier/indexBack.html.twig', [
             'pagination' => $pagination,
             'MAPBOX_API_KEY' => $mapboxApiKey,
             'loggedInUser' => $loggedInUser,

         ]);
         
     }

    #[Route('/new', name: 'app_atelier_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em, SessionInterface $session , EntityManagerInterface $entityManager): Response
{    $loggedInUserId = $session->get('user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }

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

       /* if ($existingAtelier) {
            $this->addFlash('error', "Il existe déjà un atelier le " . $atelier->getDateAtelier()->format('Y-m-d') . " avec le nom : " . $existingAtelier->getNom());
            return $this->redirectToRoute('app_atelier_new');
        }*/

        // Gestion de l'upload de la photo
        $photoFile = $form->get('photo')->getData();

        if ($photoFile instanceof UploadedFile) {
            dump($photoFile); 
            
            $uploadsDirectory = $this->getParameter('atelier_directory'); // Vérifie que ce paramètre est bien défini dans services.yaml
            $newFilename = uniqid().'.'.$photoFile->guessExtension();

            try {
                $photoFile->move($uploadsDirectory, $newFilename);
                $atelier->setPhoto($newFilename); // On stocke le nom du fichier dans l'entité Atelier
            } catch (FileException $e) {
                $this->addFlash('error', "Erreur lors de l'upload de l'image.");
            }
        } else {
            dump('No file uploaded'); 
        }
        $atelier->setNbrplacedispo($atelier->getCapaciteMax() ?? 0);

        // Sauvegarde de l’atelier en base de données
        $em->persist($atelier);
        $em->flush();

        $this->addFlash('success', "L'atelier a été ajouté avec succès.");
        return $this->redirectToRoute('app_atelier_indexBack', [], Response::HTTP_SEE_OTHER);

    }

    return $this->render('atelier/new.html.twig', [
        'atelier' => $atelier,
        'form' => $form->createView(), 
        'loggedInUser' => $loggedInUser,

    ]);
}
      
    #[Route('/{id}', name: 'app_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier,  EntityManagerInterface $entityManager, SessionInterface $session ): Response
    {$loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('atelier/show.html.twig', [
            'atelier' => $atelier,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_atelier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager ,SessionInterface $session): Response
    {   $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
       
         $user = $session->get('user');

        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_atelier_indexBack', [], Response::HTTP_SEE_OTHER);
        }
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Load from .env

        return $this->render('atelier/edit.html.twig', [
            'atelier' => $atelier,
'form' => $form->createView(),
            'user' => $user,
            'MAPBOX_API_KEY' => $mapboxApiKey,

            'loggedInUser' => $loggedInUser,


        ]);
    }

    #[Route('/atelier/delete/{id}', name: 'app_atelier_back_delete', methods: ['GET'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {$loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $entityManager->remove($atelier);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_atelier_indexBack', [], Response::HTTP_SEE_OTHER);

    }

  /*  #[Route('/ateliers/list', name: 'atelier_list_ajax')]
    public function listAteliers(Request $request, AtelierRepository $atelierRepo , EntityManagerInterface $entityManager, SessionInterface $session): Response
    {    $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', '');
    
        $query = $atelierRepo->createQueryBuilder('a');
    
        if ($search) {
            $query->andWhere('a.nom LIKE :search OR a.description LIKE :search')
                  ->setParameter('search', '%' . $search . '%');
        }
    
        if ($sort === 'asc') {
            $query->orderBy('a.nom', 'ASC');
        } elseif ($sort === 'desc') {
            $query->orderBy('a.nom', 'DESC');
        }
    
        $ateliers = $query->getQuery()->getResult();
    
        return $this->render('atelier/_list.html.twig', [
            'ateliers' => $ateliers,
            'loggedInUser' => $loggedInUser,

        ]);
    }*/
    #[Route('/atelier/list', name: 'atelier_list_ajax')]
    public function listAteliers(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, PaginatorInterface $paginator): Response
    {   
        // Vérification de la session de l'utilisateur
        $loggedInUserId = $session->get('user_id');
            
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        // Récupération de l'utilisateur
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        // Récupération des paramètres de tri et de recherche
        $sortOrder = $request->query->get('sort', '');
        $searchQuery = $request->query->get('search', '');
    
        // Récupération des ateliers avec un tri et une recherche si nécessaire
        $atelierRepository = $entityManager->getRepository(Atelier::class);
        $queryBuilder = $atelierRepository->createQueryBuilder('a');
    
        if (!empty($searchQuery)) {
            $queryBuilder->andWhere('a.nom LIKE :search')
                         ->setParameter('search', '%' . $searchQuery . '%');
        }
    
        if ($sortOrder === "asc") {
            $queryBuilder->orderBy('a.nom', 'ASC');
        } elseif ($sortOrder === "desc") {
            $queryBuilder->orderBy('a.nom', 'DESC');
        }
    
        // Pagination avec KnpPaginator
        $pagination = $paginator->paginate(
            $queryBuilder, // La requête avec les filtres de recherche et tri
            $request->query->getInt('page', 1), // Le numéro de la page
            4 // Le nombre d'éléments par page
        );
    
        // Récupération de la clé API de Mapbox
        $mapboxApiKey = $_ENV['MAPBOX_API_KEY']; // Charger depuis le fichier .env
        
        // Rendu de la vue avec les données
        return $this->render('atelier/_list.html.twig', [
            'pagination' => $pagination,
            'MAPBOX_API_KEY' => $mapboxApiKey,
            'loggedInUser' => $loggedInUser,
        ]);
    }


public function deleteAtelierAjax(Request $request , AtelierRepository $atelierRepository , EntityManagerInterface $entityManager , SessionInterface $session): JsonResponse
    {   $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        // Récupérer l'ID envoyé dans la requête (au format JSON)
        $data = json_decode($request->getContent(), true);

        if (isset($data['id'])) {
            $atelierId = $data['id'];
            $atelier = $this->$atelierRepository->find($atelierId);

            if ($atelier) {
                // Supprimer l'atelier
                $this->$entityManager->remove($atelier);
                $this->$entityManager->flush();

                return new JsonResponse(['success' => true]);
            }
        }

        return new JsonResponse(['success' => false, 'message' => 'Atelier non trouvé ou ID invalide'], 400);
    }

}
