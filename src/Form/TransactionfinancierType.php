<?php

namespace App\Form;

use App\Entity\Transactionfinancier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;  // <-- Ajoutez cette ligne

class TransactionfinancierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('description')
            ->add('datetransaction', null, [
                'widget' => 'single_text'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de transaction',
                'choices' => [
                    'Dépense' => 'Dépense',
                    'Revenu' => 'Revenu',
                ],
                'placeholder' => 'Sélectionnez un type',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un type de transaction.']),
                ],
            ])
            ->add('nbrheure');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transactionfinancier::class,
        ]);
    }
}
