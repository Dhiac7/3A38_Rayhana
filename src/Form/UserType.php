<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('prenom')
        ->add('cin')
        ->add('photo', UrlType::class, [
            'label' => 'Photo URL',
            'required' => false, 
            //'attr' => ['placeholder' => 'https://www.w3schools.com/html/pic_trulli.jpg']
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
        ->add('mdp')
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
