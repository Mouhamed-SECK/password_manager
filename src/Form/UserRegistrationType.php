<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Groupe;
use App\Entity\Role;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;

class UserRegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                "lastname",
                TextType::class,
                $this->getConfiguration("Nom", "Votre Nom", "form-control")
            )
            ->add(
                "firstName",
                TextType::class,
                $this->getConfiguration(
                    "Prenom",
                    "Votre Prenom",
                    "form-control"
                )
            )
            ->add(
                "email",
                EmailType::class,
                $this->getConfiguration("Email", "Votre Email", "form-control")
            )
            ->add("groupe", EntityType::class, [
                "class" => Groupe::class,
            ])
            ->add("roles", ChoiceType::class, [
                "choices" => [
                    "Utilisateur" => "ROLE_USER",
                    "Super Admin" => "ROLE_SUPER_ADMIN",
                    "Administrateur" => "ROLE_ADMIN",
                ],
                "expanded" => true,
                "multiple" => true,
                "label" => "Rôles",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class,
        ]);
    }
}
