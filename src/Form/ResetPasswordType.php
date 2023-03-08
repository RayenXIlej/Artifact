<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', PasswordType::class, [
            'label' => 'mot de passe',
            'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation.',
            'help' => 'Le mot de passe doit contenir 8.',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner un mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                    // max length allowed by Symfony for security reasons
                    'max' => 255,
                    'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.'
                ]),
            ]
        ])
    ->add('confirm_password', PasswordType::class, [
            'label' => 'mot de passe',
            'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation.',
            'help' => 'Le mot de passe doit contenir 8 caractères.',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner un mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                    // max length allowed by Symfony for security reasons
                    'max' => 255,
                    'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.'
                ])
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
