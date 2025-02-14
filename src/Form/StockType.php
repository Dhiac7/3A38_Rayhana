<?php
// src/Form/StockType.php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;// Ajouter cette ligne
use Symfony\Component\Form\Extension\Core\Type\FileType;  // Importation correcte de FileType
use Symfony\Component\Validator\Constraints\File;


class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Entrez le nom du produit'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du produit est obligatoire']),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'Le nom doit contenir uniquement des lettres et des espaces'
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
            ])
          
           
            ->add('date_stock', DateType::class, [
                'label' => 'Date de stock',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de stock est obligatoire'])
                ],
                'empty_data' => (new \DateTime())->format('Y-m-d'), // Valeur par défaut : aujourd'hui
            ])
          
         
            ->add('date_expiration', DateType::class, [
                'label' => 'Date d\'expiration',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date d\'expiration est obligatoire']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date d\'expiration doit être supérieure à la date actuelle'
                    ])
                ],
                'empty_data' => (new \DateTime())->modify('+1 day')->format('Y-m-d'), // Valeur par défaut : demain
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu de stockage',
                'attr' => ['placeholder' => 'Entrez le lieu de stockage'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le lieu de stockage est obligatoire'])
                ],
            ])
            ->add('conditionn', ChoiceType::class, [
                'label' => 'Condition de stockage',
                'choices' => [
                    'Sec' => 'Sec',
                    'Réfrigéré' => 'Réfrigéré',
                    'Congelé' => 'Congelé'
                ],
                'placeholder' => 'Sélectionnez une condition',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La condition de stockage est obligatoire'])
                ],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'En rupture' => 'En rupture',
                    'Périmé' => 'Périmé'
                ],
                'placeholder' => 'Sélectionnez un statut',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire'])
                ],
            ]);
          
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
            'attr' => ['novalidate' => 'novalidate'], // Désactive la validation HTML5 pour utiliser Symfony
        ]);
    }
}
