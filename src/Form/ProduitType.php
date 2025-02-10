<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('prix_vente')
            ->add('quantite_vendues')
            ->add('enPromotion')
            ->add('pourcentage_promo')
            ->add('date_debut_promo', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin_promo', null, [
                'widget' => 'single_text',
            ])
            ->add('quantite_retourne')
            ->add('date_retour', null, [
                'widget' => 'single_text',
            ])
            ->add('statut')
            ->add('raison_retour')
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
