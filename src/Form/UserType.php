<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire'),
                    new Length(min: 2, minMessage: 'Le nom doit contenir au moins 2 caractères'),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire'),
                    new Length(min: 2, minMessage: 'Le prénom doit contenir au moins 2 caractères'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'exemple@email.com',
                ],
                'constraints' => [
                    new NotBlank(message: 'L\'email est obligatoire'),
                    new Email(message: 'Veuillez entrer un email valide'),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+216XXXXXXXX',
                ],
                'constraints' => [
                    new Regex(pattern: '/^\+?[0-9]{7,20}$/', message: 'Veuillez entrer un numéro de téléphone valide'),
                ],
            ]);

        if (!$isEdit) {
            $builder->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Entrez votre mot de passe',
                    ],
                    'constraints' => [
                        new NotBlank(message: 'Le mot de passe est obligatoire'),
                        new Length(min: 6, minMessage: 'Le mot de passe doit contenir au moins 6 caractères'),
                        new Regex(pattern: '/[A-Za-z]/', message: 'Le mot de passe doit contenir au moins une lettre'),
                        new Regex(pattern: '/[0-9]/', message: 'Le mot de passe doit contenir au moins un chiffre'),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirmez votre mot de passe',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
            ]);
        }

        $builder->add('role', ChoiceType::class, [
            'label' => 'Rôle',
            'choices' => [
                'Voyageur' => 'VOYAGEUR',
                'Administrateur' => 'ADMIN',
            ],
            'attr' => [
                'class' => 'form-select',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
