<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('date_stock', null, [
                'widget' => 'single_text',
            ])
            ->add('quantite_initiale')
            ->add('quantite_utilise')
            ->add('date_expiration', null, [
                'widget' => 'single_text',
            ])
            ->add('lieu')
            ->add('conditionn', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 'ouvert',
                    'Complet' => 'complet',
                    'Annulé' => 'annulé',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 'ouvert',
                    'Complet' => 'complet',
                    'Annulé' => 'annulé',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter un atelier']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
