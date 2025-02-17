<?php

namespace App\Form;

use App\Entity\Inspection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InspectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('id_avis')
            /*->add('date_inspection', null, [
                'widget' => 'single_text'
            ])
                */
            //->add('type_inspection')
            //->add('inspecteur_id')
            ->add('resultat')
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inspection::class,
        ]);
    }
}
