<?php

namespace App\Form\Contact;

use App\Entity\Contact\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Votre email",
                'required' => true
            ])
            ->add('name', TextType::class, [
                'label' => "Votre nom",
                'required' => true
            ])
            ->add('category', choiceType::class, [
                'label' => "Catégorie",
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    "Bug" => "Bug",
                    "Suggestion" => "Suggestion",
                    "Compte" => "Compte",
                    "Signalement" => "Signalement",
                    "Autre" => "Autre"
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => "Sujet",
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => "Votre message",
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
