<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('date_atelier', null, [
                'widget' => 'single_text',
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
            ->add('save', SubmitType::class, ['label' => 'Ajouter un atelier']);

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
