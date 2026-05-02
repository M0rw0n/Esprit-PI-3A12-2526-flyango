<?php

namespace App\Form;

use App\Entity\ProfilVoyageur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilVoyageurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('destinationPreferee', TextType::class, [
                'label' => 'Destination préférée',
                'attr' => [
                    'placeholder' => 'Ex: Djerba, Paris, Barcelone...',
                    'class' => 'form-control',
                ],
                'help' => 'Votre destination de voyage préférée',
            ])
            ->add('typeVoyage', ChoiceType::class, [
                'label' => 'Type de voyage',
                'choices' => array_flip(ProfilVoyageur::TYPE_LABELS),
                'attr' => ['class' => 'form-select'],
                'help' => 'Le type de voyage qui vous correspond le mieux',
            ])
            ->add('budget', NumberType::class, [
                'label' => 'Budget (TND)',
                'attr' => [
                    'placeholder' => 'Ex: 1500.00',
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 0.01,
                ],
                'html5' => true,
                'help' => 'Votre budget moyen par voyage en dinars tunisiens',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilVoyageur::class,
        ]);
    }
}
