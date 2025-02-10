<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('atelierid')
            //->add('idUser')
            ->add('dateReservation', null, [
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [   
                'choices' => [
                    'Ouvert' => 'confirmée',
                    'Complet' => 'annulée',
                    'Annulé' => 'en attente',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
            ])
            ->add('modePaiement', ChoiceType::class, [   
                'choices' => [
                    'enligne' => 'enligne',
                    'carte' => 'carte',
                    'espece' => 'espece',
                ],
                'placeholder' => 'Sélectionnez un mode de Paiement',
                'required' => true,
            ])
           
           // ->add('nbrPlace')
           /* ->add('Role', ChoiceType::class, [
                'choices' => [
                    'Agriculteur' => 'agriculteur',
                    'Client' => 'client',
                    'Employé' => 'employee',
                ],
                'placeholder' => 'Sélectionnez un rôle',
                'required' => true,
            ])*/
            ->add('save', SubmitType::class, ['label' => 'Confirmer']);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
