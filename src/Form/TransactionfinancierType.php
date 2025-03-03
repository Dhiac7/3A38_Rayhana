<?php

namespace App\Form;

use App\Entity\Transactionfinancier;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class TransactionfinancierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('montant', NumberType::class, [
           'label' => 'Montant (€)',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'id' => 'montant',
                ],
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
                'data' => 'Dépense', // Valeur par défaut
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
            ]);
            
           /* ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // Afficher l'email de l'utilisateur
                'label' => 'Employé',
                'placeholder' => 'Sélectionnez un employé',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u.role IN (:role)')
                        ->setParameter('role', ['livreur', 'inspecteur', 'fermier']);
                },
            ]);*/

        // Ajouter un écouteur d'événements pour gérer les changements dans le champ "user"
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Vérifier si un utilisateur est sélectionné
            if (isset($data['user']) && $data['user']) {
                // Récupérer l'utilisateur et définir le montant en fonction du salaire de l'utilisateur
                $user = $form->get('user')->getData();
                if ($user) {
                    $data['montant'] = $user->getSalaire(); // Définir le montant avec le salaire de l'utilisateur
                    $form->get('montant')->setData($user->getSalaire()); // Mettre à jour la valeur du champ
                }
            }

            // Réassigner les données modifiées au formulaire
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transactionfinancier::class,
        ]);
    }
}
