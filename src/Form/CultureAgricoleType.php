<?php

namespace App\Form;

use App\Entity\CultureAgricole;
use App\Entity\Parcelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



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
                'required' => false, // Autorise les valeurs nulles
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
            ])
            ->add('parcelles', EntityType::class, [
                'class' => Parcelle::class,
                'choice_label' => 'nom', // Adjust this based on your Parcelle entity
                'multiple' => true,
                'expanded' => false, // Set to true for checkboxes, false for multi-select dropdown
                'attr' => ['class' => 'form-check'], // Bootstrap class for checkboxes
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CultureAgricole::class,
        ]);
    }
}
