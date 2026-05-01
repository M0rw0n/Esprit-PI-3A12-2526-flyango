<?php

namespace App\Form;

use App\Entity\Hebergement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class HebergementAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length(['min' => 3, 'max' => 150, 'minMessage' => 'Minimum 3 caractères', 'maxMessage' => 'Maximum 150 caractères'])
                ]
            ])
            ->add('ville', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire']),
                    new Length(['min' => 2, 'max' => 100])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Hôtel' => 'Hôtel',
                    'Appartement' => 'Appartement',
                    'Villa' => 'Villa',
                    'Riad' => 'Riad',
                    'Resort' => 'Resort',
                    "Maison d'hôtes" => "Maison d'hôtes",
                    'Auberge' => 'Auberge'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire'])
                ]
            ])
            ->add('prixParNuit', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Range(['min' => 0, 'max' => 10000])
                ]
            ])
            ->add('capacite', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Range(['min' => 1, 'max' => 50])
                ]
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                    new Length(['min' => 5, 'max' => 200])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 1000, 'maxMessage' => 'Maximum 1000 caractères'])
                ]
            ])
            ->add('disponible', CheckboxType::class, ['required' => false])
            ->add('localisation', TextType::class, ['required' => false])
            ->add('latitude', NumberType::class, ['required' => false, 'scale' => 6])
            ->add('longitude', NumberType::class, ['required' => false, 'scale' => 6])
            ->add('chambresDisponibles', NumberType::class, ['required' => false])
            ->add('image', FileType::class, [
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '5M', 'maxSizeMessage' => 'Image trop grande (max 5MB)'])
                ]
            ])
            ->add('existing_image', HiddenType::class, ['required' => false])
            ->add('galerie_photos', FileType::class, [
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        new Image(['maxSize' => '5M'])
                    ])
                ]
            ])
            ->add('galerie_to_delete', HiddenType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hebergement::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ]);
    }
}