<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Groupe;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserRegistrationType extends ApplicationType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, $this->getConfiguration("Nom", "Votre Nom", 'form-control'))
            ->add('firstName', TextType::class, $this->getConfiguration("Prenom", "Votre Prenom", 'form-control'))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre Email", 'form-control'))
            ->add('avatar', PasswordType::class, $this->getConfiguration("Mot de passe", "Votre Mot de Passe", 'form-control'))
            ->add('groupe', EntityType::class, [
                'class' => Groupe::class
            ]);


    }       

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}