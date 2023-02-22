<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner un Nom.',
                        ]),
                        new Length([
                            'min' => 2,
                            'minMessage' => 'Votre Nom doit contenir au moins {{ limit }} caractères.',
                            'max' => 40,
                            'maxMessage' => 'Votre Nom doit contenir au maximum {{ limit }} caractères.',
                        ]),
                    ],
                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prenom',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner un Prenom.',
                        ]),
                        new Length([
                            'min' => 2,
                            'minMessage' => 'Votre Prenom doit contenir au moins {{ limit }} caractères.',
                            'max' => 40,
                            'maxMessage' => 'Votre Prenom doit contenir au maximum {{ limit }} caractères.',
                        ]),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse Email',
                    'constraints' => [
                        new Email([
                            'message' => 'L\'adresse email {{ value }} n\'est pas une adresse email valide.'
                        ]),
                        new NotBlank([
                            'message' => 'Merci de renseigner une adresse email.'
                        ]),
                    ]
                ])
                ->add('dnaissance', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd'])
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
                ->add('adresse',ChoiceType::class,
                    array('choices'=>array(
                        'Tunis'=>'Tunis',
                        'Ariana'=>'Ariana',
                        'Ben arous'=>'Ben arous',
                        'Sousse'=>'Sousse',
                        'Sfax'=>'Sfax',
                        'Monastir'=>'Monastir',
                        'Nabeul'=>'Nabeul',
                        'Mahdia'=>'Mahdia',
                        'Kairaoun'=>'Kairouan',
                        'Bizerte'=>'Bizerte',
                        'Mednine'=>'Mednine',
                        'Manouba'=>'Manouba',
                        'Gabes'=>'Gabes',
                        'Gafsa'=>'Gafsa',
                        'Jendouba'=>'Jendouba',
                        'Le kef'=>'Le kef',
                        'Sidi bouzid'=>'Sidi bouzid',
                        'Kasserine'=>'Kasserine',
                        'Seliana'=>'Seliana',
                        'Kebili'=>'Kebili',
                        'Tataouine'=>'Tataouine',
                        'Djerbe'=>'Djerba',
                        'Tozeur'=>'Tozeur'
                    ) ))
                ->add('type',ChoiceType::class,
                array('choices'=>array(
                    'petOwner'=>'petOwner',
                    'veterinaire'=>'veterinaire',
                    'petSitter'=>'petSitter'
                ) ))
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'You should agree to our terms.',
                        ]),
                    ],
                ])
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
