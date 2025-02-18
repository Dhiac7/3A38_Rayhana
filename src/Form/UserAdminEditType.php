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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserAdminEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'required' => false,
                'empty_data' => '',
                ])
            ->add('prenom', null, [
                'required' => false,
                'empty_data' => '',
                ])
            ->add('cin', null, [
                'required' => false,
                'empty_data' => '',
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
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'fermier' => 'fermier',
                    'livreur' => 'livreur',
                    'inspecteur' => 'inspecteur',
                    'agriculteur' => 'agriculteur', // Added agriculteur role
                    'client' => 'client',
                ],
                'placeholder' => 'Choisissez un rôle', // Improved placeholder text
                'required' => true,
                'attr' => ['class' => 'form-select'], // Add form-select class
            ])
            ->add('mdp', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => $options['is_new'], // Required only for new users
                'mapped' => false, // Don't map directly to entity
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Mot de passe'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['placeholder' => 'Confirmer le mot de passe'],
                ],
            ])
            ->add('email', null, [
                'required' => false,
                'empty_data' => '',
                ])
            ->add('tel', null, [
                'required' => false,
                'empty_data' => '',
                ])
            ->add('salaire', null, [
                'required' => false,
                'empty_data' => '',
                ])
            ->add('adresse', ChoiceType::class, [
                'choices' => [
                    'Ariana' => 'Ariana',
                    'Beja' => 'Beja',
                    'Ben Arous' => 'Ben Arous',
                    'Bizerte' => 'Bizerte',
                    'Gabes' => 'Gabes',
                    'Gafsa' => 'Gafsa',
                    'Jendouba' => 'Jendouba',
                    'Kairouan' => 'Kairouan',
                    'Kasserine' => 'Kasserine',
                    'Kebili' => 'Kebili',
                    'Kef' => 'Kef',
                    'Mahdia' => 'Mahdia',
                    'Manouba' => 'Manouba',
                    'Mednine' => 'Mednine',
                    'Monastir' => 'Monastir',
                    'Nabeul' => 'Nabeul',
                    'Sfax' => 'Sfax',
                    'Sidi Bouzid' => 'Sidi Bouzid',
                    'Siliana' => 'Siliana',
                    'Tataouine' => 'Tataouine',
                    'Tozeur' => 'Tozeur',
                    'Tunis' => 'Tunis',
                    'Zaghouan' => 'Zaghouan'
                ],
                'placeholder' => 'Choisir un gouvernorat',
                'attr' => ['class' => 'form-select']
            ])
            ->add('save', SubmitType::class, ['label' => 'Confirmer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}