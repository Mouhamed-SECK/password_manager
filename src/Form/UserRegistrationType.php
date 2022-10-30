<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends ApplicationType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration("Nom", "Votre Nom", 'form-input'))
            ->add('firstName', TextType::class, $this->getConfiguration("Prenom", "Votre Prenom", 'form-input'))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre Email", 'form-input'))
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Votre Mot de Passe", 'form-input'));
      
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}