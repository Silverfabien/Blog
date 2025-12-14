<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'attr' => [
                    'autocomplete' => 'email'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => "Votre mot de passe",
                'attr' => [
                    'autocomplete' => 'current-password'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
