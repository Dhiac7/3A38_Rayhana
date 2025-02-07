<?php

namespace App\Form;

use App\Entity\Dechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('dateProduction', null, [
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'resycler' => 'resycler',
                    'eliminer' => 'eliminer',
                ],
                'placeholder' => 'Sélectionnez statut',
                'required' => true,
            ])           
             ->add('date_expiration', null, [
                'widget' => 'single_text',
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter dechet']);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dechet::class,
        ]);
    }
}
