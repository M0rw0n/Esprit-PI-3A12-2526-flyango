<?php

namespace App\Form;

use App\Entity\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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

class TransportAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transportType', ChoiceType::class, [
                'choices' => [
                    'Vol' => 'FLIGHT',
                    'Train' => 'TRAIN',
                    'Bus' => 'BUS',
                    'Voiture' => 'CAR'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de transport est obligatoire'])
                ]
            ])
            ->add('companyName', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La compagnie est obligatoire']),
                    new Length(['min' => 2, 'max' => 150])
                ]
            ])
            ->add('departureCity', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville de départ est obligatoire']),
                    new Length(['min' => 2, 'max' => 100])
                ]
            ])
            ->add('arrivalCity', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville d\'arrivée est obligatoire']),
                    new Length(['min' => 2, 'max' => 100])
                ]
            ])
            ->add('departureStation', TextType::class, ['required' => false])
            ->add('arrivalStation', TextType::class, ['required' => false])
            ->add('departureDatetime', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date de départ est obligatoire'])
                ]
            ])
            ->add('arrivalDatetime', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date d\'arrivée est obligatoire'])
                ]
            ])
            ->add('duration', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire']),
                    new Length(['min' => 3, 'max' => 50])
                ]
            ])
            ->add('availableSeats', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Range(['min' => 1, 'max' => 500])
                ]
            ])
            ->add('price', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Range(['min' => 0, 'max' => 50000])
                ]
            ])
            ->add('isActive', CheckboxType::class, ['required' => false])
            ->add('amenities', TextareaType::class, ['required' => false])
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
            'data_class' => Transport::class,
            'csrf_protection' => true,
        ]);
    }
}