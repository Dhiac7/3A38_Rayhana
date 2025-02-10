<?php

namespace App\Form;

use App\Entity\Parcelle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class ParcelleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la Parcelle',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Parcelle Sud'],
                'required' => true
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 36.8065'],
                'required' => true
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 10.1815'],
                'required' => true
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (m²)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 5000'],
                'required' => true
            ])
            ->add('irrigationDisponible', ChoiceType::class, [
                'label' => 'Irrigation disponible',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => false,  // Affichage en menu déroulant <select>
                'multiple' => false, 
                'attr' => ['class' => 'form-select'], // Classe Bootstrap pour un select stylé
                'required' => true,
                'constraints' => [
                    new Assert\NotNull(['message' => 'Veuillez sélectionner une option.']),
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parcelle::class,
        ]);
    }
}
