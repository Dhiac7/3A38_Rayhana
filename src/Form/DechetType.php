<?php

namespace App\Form;

use App\Entity\Dechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type', ChoiceType::class, [
            'choices' => [
                'organique' => 'organique',
                'plastique' => 'plastique',
                'métalique' => 'métalique',
                'vegetale' => 'vegetale',

            ],
            'placeholder' => 'Sélectionnez un type',
            'required' => true,
        ])
        ->add('quantite')

        //controle de saisie sur la date 
        ->add('dateProduction', DateType::class, [
            'widget' => 'single_text',  // ou 'choice' selon la manière dont tu veux afficher la date
            'required' => false,        // Autoriser la valeur vide
            'empty_data' => null,       // Si la date est vide, la valeur -> null
            'attr' => ['placeholder' => 'YYYY-MM-DD']  // Optionnel, pour ajouter un placeholder
        ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'resycler' => 'resycler',
                    'eliminer' => 'eliminer',
                ],
                'placeholder' => 'Sélectionnez statut',
                'required' => true,
            ])           
        
            ->add('date_expiration', DateType::class, [
                'widget' => 'single_text',  // ou 'choice' selon la manière dont tu veux afficher la date
                'required' => false,        // Autoriser la valeur vide
                'empty_data' => null,       // Si la date est vide, la valeur -> null
                'attr' => ['placeholder' => 'YYYY-MM-DD']  // Optionnel, pour ajouter un placeholder
            ])
            ->add('save', SubmitType::class, ['label' => 'Confirmer']);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dechet::class,
        ]);
    }
}
