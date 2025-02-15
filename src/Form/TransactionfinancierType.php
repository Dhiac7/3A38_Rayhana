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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

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
                'constraints' => [
                    new NotBlank(['message' => 'Le montant est obligatoire.']),
                    new Positive(['message' => 'Le montant doit être positif.']),
                ],
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire.']),
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
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire.']),
                ],
            ])
            ->add('nbrheure', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre d\'heures',
                ],
                'required' => false, // Rendre ce champ facultatif
                'constraints' => [
                    new Positive(['message' => 'Le nombre d\'heures doit être positif.']),
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