<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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

class ActivityAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    new Length(['min' => 3, 'max' => 255])
                ]
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('price', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Range(['min' => 0, 'max' => 10000])
                ]
            ])
            ->add('duration', TextType::class, ['required' => false])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('capacity', NumberType::class, [
                'constraints' => [
                    new Range(['min' => 1, 'max' => 1000])
                ]
            ])
            ->add('lieu', TextType::class, ['required' => false])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Excursion' => 'Excursion',
                    'Adventure' => 'Adventure',
                    'Culture' => 'Culture',
                    'Sport' => 'Sport',
                    'Gastronomie' => 'Gastronomie',
                    'Bien-être' => 'Bien-être',
                    'Autre' => 'Autre'
                ],
                'required' => false
            ])
            ->add('actif', CheckboxType::class, ['required' => false])
            ->add('latitude', NumberType::class, ['required' => false, 'scale' => 6])
            ->add('longitude', NumberType::class, ['required' => false, 'scale' => 6])
            ->add('image', FileType::class, [
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '5M'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'csrf_protection' => true,
        ]);
    }
}