<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('quantite')
            ->add('date_stock', DateType::class, [
                'widget' => 'single_text', // Affichage sous forme de champ texte unique
            ])
            ->add('quantite_initiale')
            ->add('quantite_utilise')
            ->add('date_expiration', DateType::class, [
                'widget' => 'single_text', // Affichage sous forme de champ texte unique
            ])
            ->add('lieu')
            ->add('conditionn', ChoiceType::class, [
                'choices' => [
                    'Sec' => 'Sec',
                    'Réfrigéré' => 'Réfrigéré',
                    'Congelé' => 'Congelé',
                ],
                'placeholder' => 'Sélectionnez une condition',
                'required' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'Disponible',
                    'En Rupture' => 'En Rupture',
                    'Périmé' => 'Périmé',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter stock']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
