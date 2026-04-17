<?php

namespace App\Form;

use App\Entity\ForumSujet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;

class ForumSujetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    new Length(['min' => 5, 'max' => 150, 'minMessage' => 'Minimum 5 caractères', 'maxMessage' => 'Maximum 150 caractères'])
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Hébergement' => 'Hébergement',
                    'Circuit' => 'Circuit',
                    'Activité' => 'Activité',
                    'Conseil' => 'Conseil',
                    'Destination' => 'Destination',
                    'Transport' => 'Transport'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une catégorie'])
                ]
            ])
            ->add('author', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Votre nom est obligatoire']),
                    new Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le contenu est obligatoire']),
                    new Length(['min' => 10, 'max' => 5000, 'minMessage' => 'Minimum 10 caractères'])
                ]
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '5M', 'maxSizeMessage' => 'Image trop grande (max 5MB)'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumSujet::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}