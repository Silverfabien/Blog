<?php

namespace App\Form\Security;

use App\DTO\Security\UserEditDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Votre pseudo",
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => UserEditDTO::class
        ]);
    }
}
