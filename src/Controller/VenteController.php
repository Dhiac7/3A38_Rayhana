<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Entity\Produit;
use App\Form\VenteType;
use App\Repository\VenteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/vente')]
final class VenteController extends AbstractController
{
    // Route pour l'index
    #[Route('/', name: 'app_vente_index', methods: ['GET'])]
    public function index(Request $request, VenteRepository $venteRepository, PaginatorInterface $paginator): Response
    {
        $query = $venteRepository->findAll();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('vente/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, SessionInterface $session): Response
{
    $user = $session->get('user');
    $vente = new Vente();
    
    // Vérification de l'ID du produit
    $produitId = $request->query->get('id');
    
    if (!$produitId) {
        $this->addFlash('error', 'Aucun produit sélectionné.');
        return $this->redirectToRoute('app_vente_index'); // Redirection si l'ID est manquant
    }
    
    $produit = $produitRepository->find($produitId);
    
    if (!$produit) {
        $this->addFlash('error', 'Produit introuvable.');
        return $this->redirectToRoute('app_vente_index');
    }
    
    // Associer le produit à la vente
    $vente->setProduit($produit);
    $vente->setPrix($produit->getPrixVente()); // Prix unitaire
    $vente->setQuantite(1); // Quantité par défaut
    
    // Création du formulaire
    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $produit->getPrixVente(), // Passer le prix unitaire au formulaire
    ]);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Calculer le prix total
        $quantite = $vente->getQuantite();
        $prixUnitaire = $produit->getPrixVente();
        $vente->setPrix($quantite * $prixUnitaire);
        
        // Sauvegarde
        $entityManager->persist($vente);
        $entityManager->flush();
        
        $this->addFlash('success', 'Vente enregistrée avec succès.');
        return $this->redirectToRoute('app_vente_index');
    }
    
    // Affichage du formulaire
    return $this->render('vente/new.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
        'user' => $user,
    ]);
}

    // Route pour afficher une vente spécifique (avec contrainte sur l'ID)
    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Vente $vente): Response
    {
        return $this->render('vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

    // Route pour éditer une vente
    #[Route('/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vente/edit.html.twig', [
            'vente' => $vente,
            'form' => $form->createView(),
        ]);
    }

    // Route pour supprimer une vente
    #[Route('/{id}', name: 'app_vente_delete', methods: ['POST'])]
    public function delete(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }
}