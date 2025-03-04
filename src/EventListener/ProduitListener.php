<?php
namespace App\EventListener;

use App\Entity\Produit;
use App\Service\StockService;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ProduitListener
{
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    // Cette méthode est exécutée juste avant la mise à jour d'une entité
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        
        // Vérifier si l'entité est bien un Produit
        if (!$entity instanceof Produit) {
            return;
        }
        
        // Vérifier si le champ quantité a été modifié
        if ($args->hasChangedField('quantite')) {
            $newQuantite = $args->getNewValue('quantite');
            
            // On envoie une notification UNIQUEMENT si la nouvelle quantité est <= 20
            if ($newQuantite <= 20) {
                $this->stockService->notifyLowStock($entity);
            }
        }
    }
}