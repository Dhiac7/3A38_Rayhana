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
                    'id' => 'vente_quantite', // Ajouter un ID pour JavaScript
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix Total (€)',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true, // Champ en lecture seule
                    'id' => 'vente_prix', // ID pour JavaScript
                ],
            ])
            ->add('prixUnitaire', HiddenType::class, [
                'mapped' => false, // Ce champ n'est pas mappé à l'entité
                'data' => $options['prix_unitaire'], // Utilisation de l'option passée
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
            'prix_unitaire' => null, // Définition de l'option personnalisée
        ]);
    }
}