<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                ->add('nom')
                ->add('prenom')
                ->add('email')
                ->add('dnaissance', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd'])
                ->add('password',RepeatedType::class, [
                    'type' =>PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
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
                ->add('ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
