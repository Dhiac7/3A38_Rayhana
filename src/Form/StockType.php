<?php
// src/Form/StockType.php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Entrez le nom du produit'],
            ])


            ->add('date_stock', DateType::class, [
                'widget' => 'single_text', // Utilisation d'un champ HTML5 pour une meilleure compatibilité
                'html5' => true,          // Active le support des navigateurs modernes
                'required' => true,  
                'empty_data' =>(new \DateTime())->format('Y-m-d H:i'),    // Rend le champ obligatoire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date'
                ],
            ])

            ->add('date_expiration', DateType::class, [
                'widget' => 'single_text', // Utilisation d'un champ HTML5 pour une meilleure compatibilité
                'html5' => true,          // Active le support des navigateurs modernes
                'required' => true,  
                'empty_data' =>(new \DateTime())->format('Y-m-d H:i'),    // Rend le champ obligatoire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date'
                ],
            ])


        
            ->add('lieu', TextType::class, [
                'label' => 'Lieu de stockage',
                'attr' => ['placeholder' => 'Entrez le lieu de stockage'],
            ])
            ->add('conditionn', ChoiceType::class, [
                'label' => 'Condition de stockage',
                'choices' => [
                    'Sec' => 'Sec',
                    'Réfrigéré' => 'Réfrigéré',
                    'Congelé' => 'Congelé'
                ],
                'placeholder' => 'Sélectionnez une condition',
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'En rupture' => 'En rupture',
                    'Périmé' => 'Périmé'
                ],
                'placeholder' => 'Sélectionnez un statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
