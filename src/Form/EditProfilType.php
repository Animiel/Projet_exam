<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail : ',
            ])
            // ->add('roles')
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe* : '],
                'second_options' => ['label' => 'Confirmer mot de passe* : '],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Veillez à entrer un minimum de 8 caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!\.%$\/^&()\[\]{}:;,\?~_\+\-=\\\|§µ£]).{8,}$/', 'Veillez à utiliser au moins un chiffre, une lettre minuscule ET majuscule, un caractère spécial.'),
                    // new Regex('/^[0-9]+$/', 'Pas de lettres ou caractères spéciaux acceptés.'),

                ],
            ])
            // ->add('isVerified')
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo : ',
            ])
            ->add('naissance', DateType::class, [
                'label' => 'Date de naissance : ',
                'widget' => 'single_text',
            ])
            // ->add('dateInscription')
            // ->add('banni')
            // ->add('annonceFavorites')
            // ->add('sujetFavorites')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
