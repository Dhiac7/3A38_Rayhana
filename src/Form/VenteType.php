<?php


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
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix Total (€)',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true, // Empêche la modification manuelle
                    'id' => 'prix_total', // Id pour JavaScript
                ],
            ])
            ->add('prixUnitaire', HiddenType::class, [
                'mapped' => false, // Ce champ n'est pas mappé à l'entité
                'data' => $options['prix_unitaire'], // Utilisation de l'option passée
                'attr' => ['id' => 'prix_unitaire'],
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
                'label' => 'Valider la vente',
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