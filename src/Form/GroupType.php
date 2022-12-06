<?php

namespace App\Form;

use App\Entity\Groupe;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                "title",
                TextType::class,
                $this->getConfiguration(
                    "Label du groupe",
                    "Nom du group",
                    "form-control"
                )
            )
            ->add(
                "privateKey",
                PasswordType::class,
                $this->getConfiguration(
                    "Votre Mot de Passe de chiffrement",
                    "Votre Mot de Passe de chiffrement",
                    "form-control"
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Groupe::class,
        ]);
    }
}
