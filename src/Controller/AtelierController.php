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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            6 // nbr element par page 
        );
        return $this->render('atelier/index.html.twig', [
            'pagination' => $pagination,
        ]);
        /*return $this->render('atelier/index.html.twig', [
            'ateliers' => $atelierRepository->findAll(),
        ]);*/
    }
    #[Route(name: 'app_atelier_indexBack', methods: ['GET'])]
    // public function index(AtelierRepository $atelierRepository): Response
    public function indexBack(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator): Response
     {
         $query = $atelierRepository->findAll();
         $pagination = $paginator->paginate(
             $query, // Donneili bch namlou pagination
             $request->query->getInt('page', 1), // Num page 
             6 // nbr element par page 
         );
         return $this->render('atelier/indexBack.html.twig', [
             'pagination' => $pagination,
         ]);
         /*return $this->render('atelier/index.html.twig', [
             'ateliers' => $atelierRepository->findAll(),
         ]);*/
     }

    #[Route('/new', name: 'app_atelier_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
{
    $user = $session->get('user');
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
            return $this->redirectToRoute('app_atelier_new');
        }

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

        // Sauvegarde de l’atelier en base de données
        $em->persist($atelier);
        $em->flush();

        $this->addFlash('success', "L'atelier a été ajouté avec succès.");
        return $this->redirectToRoute('app_atelier_indexBack', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('atelier/new.html.twig', [
        'atelier' => $atelier,
        'form' => $form,
        'user' => $user,
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
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager ,SessionInterface $session): Response
    {       $user = $session->get('user');

        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);
        dump($form->getData()->getDateAtelier());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('atelier/edit.html.twig', [
            'atelier' => $atelier,
'form' => $form->createView(),
            'user' => $user,

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

    #[Route('/ateliers/list', name: 'atelier_list_ajax')]
    public function listAteliers(Request $request, AtelierRepository $atelierRepo): Response
    {
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
        ]);
    }
    
}
