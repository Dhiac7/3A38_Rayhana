<?php

// src/Form/VenteType.php
namespace App\Form;

use App\Entity\Vente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix',
                ],
            ])
            ->add('methodepayement', ChoiceType::class, [
                'choices' => [
                    'Espèces' => 'espèces',
                    'Carte bancaire' => 'carte_bancaire',
                    'Chèque' => 'chèque',
                    'Virement' => 'virement',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
