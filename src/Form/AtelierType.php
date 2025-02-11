<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('date_atelier', DateTimeType::class, [
                'widget' => 'single_text', // Utilisation d'un champ HTML5 pour une meilleure compatibilité
                'html5' => true,          // Active le support des navigateurs modernes
                'required' => true,       // Rend le champ obligatoire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date'
                ],
            ])
            ->add('capacite_max')
            ->add('prix')
           // ->add('idUser')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 'ouvert',
                    'Complet' => 'complet',
                    'Annulé' => 'annulé',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Confirmer']);

           /* ->add('Role', ChoiceType::class, [
                'choices' => [
                    'Agriculteur' => 'agriculteur',
                    'Client' => 'client',
                    'Employé' => 'employee',
                ],
                'placeholder' => 'Sélectionnez un rôle',
                'required' => true,
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Atelier::class,
        ]);
    }
}
