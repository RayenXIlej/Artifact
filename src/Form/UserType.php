<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

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
                ->add('numtel')
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
                ->add('type',ChoiceType::class, [
                    'choices' => [
                        'petOwner' => 'petOwner',
                        'petSitter' => 'petSitter',
                        'veterinaire' => 'veterinaire',
                    ],
                    
                ])
                ->add('diplome', FileType::class, [
                    'label' => 'diplome (PDF file)',
    
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
    
                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required' => false,
    
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid PDF document',
                        ])
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'You should agree to our terms.',
                        ]),
                    ],
                ])
                // ->add('captcha', Recaptcha3Type::class, [
                //     'constraints' => new Recaptcha3(),
                //     'action_name' => 'security_registration',
                // ])

                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
