<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('prenom')
        ->add('cin')
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
        ->add('role', ChoiceType::class, [
            'choices' => [
                'agriculteur' => 'agriculteur',
                'client' => 'client',
                'fermier' => 'fermier',
                'livreur' => 'livreur',
                'inspecteur' => 'inspecteur',
            ],
            'placeholder' => 'entrez votre role',
            'required' => true,
        ])
        ->add('mdp', PasswordType::class) 
        ->add('email')
        ->add('tel')
        ->add('save', SubmitType::class, ['label' => 'Confirmer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

