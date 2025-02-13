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

#[Route('/vente')]
final class VenteController extends AbstractController
{
    #[Route(name: 'app_vente_index', methods: ['GET'])]
    public function index(Request $request, VenteRepository $venteRepository, PaginatorInterface $paginator): Response
    {
        $query = $venteRepository->findAll();
        $pagination = $paginator->paginate(
            $query, // Donnée pour la pagination
            $request->query->getInt('page', 1), // Numéro de page
            6 // Nombre d'éléments par page
        );

        return $this->render('vente/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

   
    #[Route('/new', name: 'app_vente_new')]
public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
{
    $vente = new Vente();
    
    // Fetch the product ID from the request
    $produitId = $request->query->get('id');
    $produit = $produitRepository->find($produitId);
    
    if ($produit) {
        // Associate the product with the sale
        $vente->setProduit($produit);
        
        // Pre-fill the price and quantity fields with the product's data
        $vente->setPrix($produit->getPrixVente()); // Set the initial price
        $vente->setQuantite(1); // Default quantity
    }
    
    // Create the form
    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $produit->getPrixVente(), // Récupère le prix du produit
    ]);
    
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Calculate the total price based on quantity
        $quantite = $vente->getQuantite();
        $prixUnitaire = $produit->getPrixVente();
        $vente->setPrix($quantite * $prixUnitaire);
        
        // Persist the sale
        $entityManager->persist($vente);
        $entityManager->flush();
    
        // Redirect to the sales index page
        return $this->redirectToRoute('app_vente_index');
    }
    
    // Render the template with the form and product data
    return $this->render('vente/new.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit, // Pass the product to the template
    ]);
}
    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'])]
    public function show(Vente $vente): Response
    {
        return $this->render('vente/show.html.twig', [
            'vente' => $vente,
        ]);
    }

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

    #[Route('/{id}', name: 'app_vente_delete', methods: ['POST'])]
    public function delete(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }
}
