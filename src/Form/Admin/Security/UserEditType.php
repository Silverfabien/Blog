<?php

namespace App\Form\Admin\Security;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('role', choiceType::class, [
                'label' => "Catégorie",
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    "Utilisateur" => "ROLE_USER",
                    "Ami" => "ROLE_FRIEND",
                    "Auteur" => "ROLE_AUTHOR",
                    "Modérateur" => "ROLE_MODERATOR",
                    "Admin" => "ROLE_ADMIN"
                ]
            ])
            ->add('picture', CheckboxType::class, [
                'label' => "Supprimer l'image de profil",
                'required' => false
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
