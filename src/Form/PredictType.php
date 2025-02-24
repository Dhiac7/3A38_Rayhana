<?php
// src/Form/PredictType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PredictType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('input_data', FileType::class, [
                'label' => 'Choisissez une image',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
                        'mimeTypesMessage' => 'Merci de télécharger un fichier image valide (PNG, JPG, JPEG)',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Prédire']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

