<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('stock', EntityType::class, [
            'class' => Stock::class,
            'choice_label' => 'nom',
        ])
            ->add('quantite')
          
            
            ->add('prix_vente')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'Disponible',
                    'En Rupture' => 'En Rupture',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
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
       
            ->add('raison_retour', ChoiceType::class, [
                'choices' => [
                    'Erreur de livraison' => 'Erreur de livraison',
                    'Produit endommagé' => 'Produit endommagé',
                ],
                'placeholder' => 'Sélectionnez une raison',
                'required' => true,
            ])
            
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'nom',
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
