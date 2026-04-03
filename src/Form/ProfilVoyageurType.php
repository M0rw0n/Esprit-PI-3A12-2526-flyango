<?php

namespace App\Form;

use App\Entity\ProfilVoyageur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProfilVoyageurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('destinationPreferee', TextType::class, [
                'label' => '📍 Destination préférée',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Marrakech, Paris, Tokyo',
                ],
                'constraints' => [
                    new NotBlank(message: 'La destination est obligatoire'),
                ],
            ])
            ->add('typeVoyage', ChoiceType::class, [
                'label' => '✈️ Type de voyage',
                'choices' => [
                    '🏔️ Adventure' => 'Adventure',
                    '🏖️ Relaxation' => 'Relaxation',
                    '🏛️ Cultural' => 'Cultural',
                    '💼 Business' => 'Business',
                    '👨‍👩‍👧 Family' => 'Family',
                    '💑 Romantic' => 'Romantic',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le type de voyage est obligatoire'),
                ],
            ])
            ->add('budget', MoneyType::class, [
                'label' => '💰 Budget',
                'currency' => 'EUR',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0,00',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le budget est obligatoire'),
                    new Positive(message: 'Le budget doit être positif'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilVoyageur::class,
        ]);
    }
}
