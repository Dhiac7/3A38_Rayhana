<?php
// src/Form/VenteType.php

namespace App\Form;

use App\Entity\Vente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'id' => 'vente_quantite',
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix Total (€)',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'id' => 'vente_prix',
                ],
            ])
            ->add('prixUnitaire', HiddenType::class, [
                'mapped' => false,
                'data' => $options['prix_unitaire'],
                'attr' => ['id' => 'vente_prixUnitaire'],
            ])
            ->add('methodepayement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Espèces' => 'espèces',
                    'Carte bancaire' => 'carte_bancaire',
                    'Chèque' => 'chèque',
                    'Virement' => 'virement',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
            'prix_unitaire' => null,
        ]);
    }
}
