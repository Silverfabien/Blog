<?php

namespace App\Form\Article;

use App\Entity\Article\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre"
            ])
            ->add('pictureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'label' => "Image"
            ])
            ->add('description', TextAreaType::class, [
                'label' => "Description"
            ])
            ->add('content', TextAreaType::class, [
                'label' => "Contenu"
            ])
            ->add('publish', CheckboxType::class, [
                'label' => "Publier",
                'required' => false,
                'attr' => [
                    'checked' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
