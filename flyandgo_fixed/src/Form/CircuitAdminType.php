<?php

namespace App\Form;

use App\Entity\Circuit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Image;

class CircuitAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    new Length(['min' => 5, 'max' => 200])
                ]
            ])
            ->add('depart', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le départ est obligatoire']),
                    new Length(['min' => 2, 'max' => 100])
                ]
            ])
            ->add('destination', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La destination est obligatoire']),
                    new Length(['min' => 2, 'max' => 150])
                ]
            ])
            ->add('duree', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire']),
                    new Range(['min' => 1, 'max' => 365])
                ]
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date de début est obligatoire'])
                ]
            ])
            ->add('prix', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Range(['min' => 0, 'max' => 100000])
                ]
            ])
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    'Facile' => 'Facile',
                    'Modéré' => 'Modéré',
                    'Difficile' => 'Difficile',
                    'Extrême' => 'Extrême'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La difficulté est obligatoire'])
                ]
            ])
            ->add('placesDisponibles', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Range(['min' => 1, 'max' => 5000])
                ]
            ])
            ->add('sourceType', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'admin',
                    'Personnalisé' => 'custom'
                ]
            ])
            ->add('actif', CheckboxType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('planB', TextareaType::class, ['required' => false])
            ->add('image', FileType::class, [
                'required' => $options['require_image'] ?? false,
                'constraints' => [
                    new Image(['maxSize' => '5M', 'maxSizeMessage' => 'Image trop grande (max 5MB)'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Circuit::class,
            'require_image' => false,
            'csrf_protection' => true,
        ]);
    }
}