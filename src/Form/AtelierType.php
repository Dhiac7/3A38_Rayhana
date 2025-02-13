<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('date_atelier', DateType::class, [
                'widget' => 'single_text', // Utilisation d'un champ HTML5 pour une meilleure compatibilité
                'html5' => true,          // Active le support des navigateurs modernes
                'required' => true,  
                'empty_data' =>(new \DateTime())->format('Y-m-d H:i'),    // Rend le champ obligatoire
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
            ->add('photo', FileType::class, [
                'label' => 'Téléchargez une photo',
                'required' => false,
                'mapped' => false, 
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WEBP).',
                    ])
                ],
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
