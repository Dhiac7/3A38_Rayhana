<?php

// src/Form/TransactionfinancierType.php
namespace App\Form;

use App\Entity\Transactionfinancier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionfinancierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Montant',
                ],
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Dépense' => 'Dépense',
                    'Revenu' => 'Revenu',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('nbrheure', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre d\'heures',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transactionfinancier::class,
        ]);
    }
}