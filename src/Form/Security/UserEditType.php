<?php

namespace App\Form\Security;

use App\DTO\Security\UserEditDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Votre pseudo",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 20,
                        maxMessage: "Votre pseudo peut contenir plus de {{ limit }} caractères."
                    )
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 255,
                        maxMessage: "Votre email ne peut pas contenir plus de {{ limit }} caractères."
                    )
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 50,
                        maxMessage: "Votre prénom ne peut pas contenir plus de {{ limit }} caractères."
                    )
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'required' => true,
                'constraints' => [
                    new Length(
                        max: 50,
                        maxMessage: "Votre nom ne peut pas contenir plus de {{ limit }} caractères."
                    )
                ]
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
