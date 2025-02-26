<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitRepository;
use Psr\Log\LoggerInterface;

class PanierService {
    private SessionInterface $session;
    private ProduitRepository $produitRepository;
    private LoggerInterface $logger;
    
    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository, LoggerInterface $logger)
    {
        $this->session = $requestStack->getSession();
        $this->produitRepository = $produitRepository;
        $this->logger = $logger;
    }
    
    public function ajouterProduit(int $id): void
    {
        $panier = $this->session->get('panier', []);
        
        // Vérifie si $panier[$id] est un tableau, sinon l'initialise
        if (isset($panier[$id]) && is_array($panier[$id])) {
            $panier[$id]['quantite']++; // Incrémente la quantité
        } else {
            $panier[$id] = [
                'quantite' => 1, // Initialise la quantité à 1
            ];
        }
        
        $this->session->set('panier', $panier);
    }
    
    public function supprimerProduit(int $id): void
    {
        $panier = $this->session->get('panier', []);
        
        if (isset($panier[$id])) {
            unset($panier[$id]); // Supprime le produit du panier
        }
        
        $this->session->set('panier', $panier);
    }
    
    public function diminuerProduit(int $id): void
    {
        $panier = $this->session->get('panier', []);
        
        if (isset($panier[$id]) && is_array($panier[$id])) {
            if ($panier[$id]['quantite'] > 1) {
                $panier[$id]['quantite']--; // Diminue la quantité
            } else {
                unset($panier[$id]); // Supprime le produit si la quantité est 1
            }
        }
        
        $this->session->set('panier', $panier);
    }
    
    public function getPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $panierData = [];
        $totalPanier = 0;
        
        foreach ($panier as $id => $item) {
            // Vérifie que $item est un tableau
            if (!is_array($item)) {
                $this->logger->warning("Structure incorrecte pour le produit ID : $id");
                continue;
            }
            
            $produit = $this->produitRepository->find($id);
            if ($produit) {
                $quantite = $item['quantite']; // Quantité du produit
                $prixVente = $produit->getPrixVente(); // Prix de vente du produit
                $prixTotalProduit = $prixVente * $quantite; // Prix total pour ce produit
                
                $panierData[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'prixVente' => $prixVente,
                    'prixTotalProduit' => $prixTotalProduit,
                ];
                
                $totalPanier += $prixTotalProduit; // Ajoute au total du panier
            } else {
                $this->logger->warning("Produit non trouvé avec l'ID : $id");
            }
        }
        
        // Récupérer les informations de réduction si elles existent
        $discountApplied = $this->session->get('discount_applied', false);
        $discountPercentage = $this->session->get('discount_percentage', 0);
        
        // Calculer le prix final avec la réduction si elle est appliquée
        $totalFinal = $totalPanier;
        $economie = 0;
        
        if ($discountApplied) {
            $economie = ($totalPanier * $discountPercentage) / 100;
            $totalFinal = $totalPanier - $economie;
        }
        
        return [
            'panierData' => $panierData,
            'totalPanier' => $totalPanier,
            'discountApplied' => $discountApplied,
            'discountPercentage' => $discountPercentage,
            'economie' => $economie,
            'totalFinal' => $totalFinal
        ];
    }
    
    public function viderPanier(): void
    {
        $this->session->remove('panier'); // Vide le panier
        $this->session->remove('discount_applied'); // Supprime également les infos de réduction
        $this->session->remove('discount_percentage');
        $this->session->remove('original_total');
        $this->session->remove('final_total');
    }
    
    public function appliquerReduction(float $pourcentage): void
    {
        // Stocker la réduction en session
        $this->session->set('discount_applied', true);
        $this->session->set('discount_percentage', $pourcentage);
    }
    
    public function supprimerReduction(): void
    {
        // Supprimer la réduction
        $this->session->remove('discount_applied');
        $this->session->remove('discount_percentage');
        $this->session->remove('original_total');
        $this->session->remove('final_total');
    }
}