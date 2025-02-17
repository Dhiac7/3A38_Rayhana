<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use App\Entity\User;

#[Route('/produitback')]
final class ProduitbackController extends AbstractController
{
    #[Route(name: 'app_produitback_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, SessionInterface $session, EntityManagerInterface $entityManager): Response
    { 
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('produit/produitback.html.twig', [
            'produits' => $produitRepository->findAll(),
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/new', name: 'app_produitback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, SessionInterface $session): Response
    {  
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité du produit
            $nom = $form->get('nom')->getData();
            $existingProduit = $produitRepository->findOneBy(['nom' => $nom]);

            if ($existingProduit) {
                $form->get('nom')->addError(new FormError('Ce produit existe déjà.'));
            } else {
                // Gestion de l’image
                $photoFile = $form->get('image')->getData();

                if ($photoFile instanceof UploadedFile) {
                    $uploadsDirectory = $this->getParameter('image_directory'); 
                    $newFilename = uniqid().'.'.$photoFile->guessExtension();
                    $photoFile->move($uploadsDirectory, $newFilename);
                    $produit->setImage($newFilename);
                }

                $entityManager->persist($produit);
                $entityManager->flush();
                return $this->redirectToRoute('app_produitback_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/{id}', name: 'app_produitback_show', methods: ['GET'])]
    public function show(Produit $produit, SessionInterface $session, EntityManagerInterface $entityManager): Response
    { 
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('produit/showback.html.twig', [
            'produit' => $produit,
            'loggedInUser' => $loggedInUser,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SessionInterface $session): Response
    { 
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l’image
            $photoFile = $form->get('image')->getData();
            
            if ($photoFile instanceof UploadedFile) {
                $uploadsDirectory = $this->getParameter('image_directory'); 
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($uploadsDirectory, $newFilename);
                $produit->setImage($newFilename);
            }
            
            $entityManager->flush();
            return $this->redirectToRoute('app_produitback_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }
    

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager, SessionInterface $session): Response
    { 
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produitback_index', [], Response::HTTP_SEE_OTHER);
    }
}
