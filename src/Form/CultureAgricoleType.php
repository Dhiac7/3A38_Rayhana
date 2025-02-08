<?php

namespace App\Form;

use App\Entity\CultureAgricole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;


class CultureAgricoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de la culture'],
                'label' => 'Nom'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Céréale' => 'céréale',
                    'Légume' => 'légume',
                    'Fruit' => 'fruit',
                    'Oléagineux' => 'oléagineux',
                    'Légumineuse' => 'légumineuse',
                    'Autre' => 'autre',
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Type'
            ])
            ->add('dateSemi', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de semis',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de semis ne peut pas être dans le futur.',
                    ])
                ],
            ])
            ->add('superficie', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Superficie en hectares'],
                'label' => 'Superficie'
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En croissance' => 'en croissance',
                    'Mature' => 'mature',
                    'Récolté' => 'récolté',
                    'Malade' => 'malade',
                    'En attente' => 'en attente',
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Statut'
            ])
            ->add('rendementEstime', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Rendement estimé'],
                'label' => 'Rendement estimé'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CultureAgricole::class,
        ]);
    }
}
