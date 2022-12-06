<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    public function getConfiguration(
        $label,
        $placeholder,
        $cssClass = null,
        $entity = null
    ) {
        return [
            "label" => $label,
            "attr" => [
                "placeholder" => $placeholder,
                "class" => $cssClass,
            ],
        ];
    }
}
