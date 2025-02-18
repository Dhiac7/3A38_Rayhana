<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le stock est obligatoire.'])
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\Positive(['message' => 'La quantité doit être positive.']),
                ],
            ])
            ->add('prix_vente', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix de vente est obligatoire.']),
                    new Assert\Positive(['message' => 'Le prix de vente doit être positif.']),
                ],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'Disponible',
                    'En rupture' => 'En rupture',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire.']),
                ],
            ])
            ->add('enPromotion', CheckboxType::class, [
                'required' => false,
            ])
            ->add('pourcentage_promo', null, [
                'required' => false,
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le pourcentage de promo doit être compris entre 0 et 100.',
                    ]),
                ],
            ])
            ->add('date_debut_promo', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de début de promotion doit être aujourd\'hui ou plus tard.',
                    ]),
                ],
            ])
            ->add('date_fin_promo', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du produit est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z]+$/u',
                        'message' => 'Le nom ne doit contenir que des lettres.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Téléchargez une photo',
                'required' => true, // Rend le champ obligatoire dans le formulaire
                'mapped' => false, 
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'image est obligatoire.']), // Ajoute la contrainte
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WEBP).',
                    ])
                ],
            ])
            
            ->add('description_globale', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description globale est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/[a-zA-Z]/',
                        'message' => 'La description globale doit contenir au moins une lettre.',
                    ]),
                ],
            ])
            ->add('description_detaille', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description détaillée est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/[a-zA-Z]/',
                        'message' => 'La description détaillée doit contenir au moins une lettre.',
                    ]),
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Fruits' => 'Fruits',
                    'Légumes' => 'Légumes',
                    'Déchets' => 'Déchets',
                ],
                'placeholder' => 'Sélectionnez une catégorie',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La catégorie est obligatoire.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'constraints' => [
                new Assert\Callback([$this, 'validatePromo']),
            ],
        ]);
    }

    public function validatePromo(Produit $produit, ExecutionContextInterface $context): void
    {
        if ($produit->isEnPromotion()) {
            if (!$produit->getPourcentagePromo() || !$produit->getDateDebutPromo() || !$produit->getDateFinPromo()) {
                $context->buildViolation('Tous les champs de promotion sont obligatoires si le produit est en promotion.')
                    ->atPath('enPromotion')
                    ->addViolation();
            }

            if ($produit->getDateDebutPromo() && $produit->getDateFinPromo() && $produit->getDateFinPromo() <= $produit->getDateDebutPromo()) {
                $context->buildViolation('La date de fin de promotion doit être après la date de début.')
                    ->atPath('date_fin_promo')
                    ->addViolation();
            }
        }
    }
}
