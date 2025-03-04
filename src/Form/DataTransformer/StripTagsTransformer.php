<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StripTagsTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transformation du modèle vers le formulaire (ici, on laisse tel quel)
        return $value;
    }

    public function reverseTransform($value)
    {
        // Transformation inverse : on retire les balises HTML
        return strip_tags($value);
    }
}
