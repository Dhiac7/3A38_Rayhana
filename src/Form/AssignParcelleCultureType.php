<?php

// src/Form/AssignParcelleCultureType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Parcelle;
use App\Entity\CultureAgricole;

class AssignParcelleCultureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parcelle', ChoiceType::class, [
                'choices' => $options['parcelles'],
                'choice_label' => function (Parcelle $parcelle) {
                    return $parcelle->getName();  // Adjust based on your model
                },
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('cultureAgricole', ChoiceType::class, [
                'choices' => $options['cultures'],
                'choice_label' => function (CultureAgricole $culture) {
                    return $culture->getName();  // Adjust based on your model
                },
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'parcelles' => [],
            'cultures' => [],
        ]);
    }
}
