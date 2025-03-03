<?php
// src/Service/StockService.php

namespace App\Service;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TwilioService;

class StockService
{
    private EntityManagerInterface $entityManager;
    private TwilioService $twilioService;

    public function __construct(EntityManagerInterface $entityManager, TwilioService $twilioService)
    {
        $this->entityManager = $entityManager;
        $this->twilioService = $twilioService;
    }

    public function notifyLowStock(Produit $product): void
    {
        $message = "ALERTE STOCK BAS : La quantité du produit {$product->getNom()} est de {$product->getQuantite()} unités (seuil critique: 20).";
        $this->twilioService->sendSms($_ENV['ADMIN_PHONE_NUMBER'], $message);
    }
    
    // Garde la méthode originale pour la compatibilité
    public function checkProductQuantity(int $productId): void
    {
        $product = $this->entityManager->getRepository(Produit::class)->find($productId);

        if (!$product) {
            throw new \Exception("Produit non trouvé !");
        }

        // Si la quantité est inférieure ou égale à 20
        if ($product->getQuantite() <= 20) {
            $message = "Alerte : La quantité du produit {$product->getNom()} est inférieure ou égale à 20.";
            $this->twilioService->sendSms($_ENV['ADMIN_PHONE_NUMBER'], $message);
        }
    }
}