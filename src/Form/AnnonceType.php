<?php

namespace App\Form;

use App\Entity\Motif;
use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pet_genre', ChoiceType::class, [
                'choices' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Sexe* : ',
            ])
            ->add('images', FileType::class, [
                'label' => 'Images de votre animal* : ',
                'multiple' => true,
                'required' => true,
            ])
            ->add('pet_name', TextType::class, [
                'label' => 'Nom de votre animal* : ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Rex, Lola, ...',
                ],
                'constraints' => [
                    new Regex('/^[a-zA-Z]*$/', 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.'),
                    new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
                ],
            ])
            // ->add('localisation', TextType::class, [
            //     'label' => 'Ville* : ',
            //     'required' => true,
            //     'attr' => [
            //         'placeholder' => 'Paris, Londres, ...',
            //     ],
            //     'constraints' => [
            //         new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
            //     ]
            // ])
            // ->add('compLocal', TextType::class, [
            //     'label' => 'Compléments de localisation : ',
            //     'required' => false,
            //     'attr' => [
            //         'placeholder' => 'Place de la Bastille, Neuhof, Rue des champs...',
            //     ],
            //     'constraints' => [
            //         new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
            //     ]
            // ])
            ->add('pet_befriends', TextareaType::class, [
                'label' => 'Affinités de votre animal : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ne tolère pas les...',
                ],
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_health', TextareaType::class, [
                'label' => 'Informations concernant la santé : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Allergie au croquette au boeuf...',
                ],
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_caractere', TextareaType::class, [
                'label' => 'Informations sur le caractère ou diverses : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Calme et très joueur...',
                ],
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_age', TextType::class, [
                'label' => 'Âge de votre animal* : ',
                'required' => true,
                'attr' => [
                    'placeholder' => '10',
                ],
                'constraints' => [
                    new Regex('/^[0-9]+$/', 'Pas de lettres ou caractères spéciaux acceptés.'),
                    new Length(['max' => 3, 'maxMessage' => 'Maximum 3 caractères requis.']),
                ],
            ])
            ->add('motifAnnonce', EntityType::class, [
                'label' => 'Le motif de votre annonce* : ',
                'required' => true,
                'class' => Motif::class,
                'choice_label' => 'name',
            ])
            ->add('descImg', TextType::class, [
                'label' => 'Courte description générale de vos images* : ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'chat noir et blanc couché dans l\'herbe, labrador brun tenant un baton dans la bouche, ...',
                ],
            ])
            ->add('latitude', HiddenType::class, [
                'required' => true,
            ])
            ->add('longitude', HiddenType::class, [
                'required' => true,
            ])
            // ->add('annonceUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
