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

class UserType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = [];
        for ($i = 1990; $i <= date("Y"); $i++) {
            $years[$i] = $i;
        }
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
        ->add('mdp', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'options' => [
                'attr' => ['class' => 'form-control'],
            ],
            'required' => true,
            'first_options'  => [
                'label' => 'Mot de passe',
                'attr' => ['placeholder' => 'Mot de passe'],
            ],
            'second_options' => [
                'label' => 'Confirmer le mot de passe',
                'attr' => ['placeholder' => 'Confirmer le mot de passe'],
            ],
        ])        
        ->add('email')
        ->add('tel')
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
        ->add('genre', ChoiceType::class, [
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ],
            'placeholder' => 'Choisir un genre',
            'attr' => ['class' => 'form-select']
        ])
        ->add('AnneeNaissance', ChoiceType::class, [
            'choices' => $years,
            'placeholder' => 'Choisir une année de naissance',
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

