<?php

namespace App\Form;

use App\Entity\CultureAgricole;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class CultureAgricoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Céréale' => 'céréale',
                'Légume' => 'légume',
                'Fruit' => 'fruit',
                'Oléagineux' => 'oléagineux',
                'Légumineuse' => 'légumineuse',
                'Autre' => 'autre',
            ],
        ])
        ->add('dateSemi', null, [
            'widget' => 'single_text',
        ])
        ->add('superficie')
        ->add('statut', ChoiceType::class, [
            'choices' => [
                'En croissance' => 'en croissance',
                'Mature' => 'mature',
                'Récolté' => 'récolté',
                'Malade' => 'malade',
                'En attente' => 'en attente',
            ],
        ])
        ->add('rendementEstime')
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CultureAgricole::class,
        ]);
    }
}
