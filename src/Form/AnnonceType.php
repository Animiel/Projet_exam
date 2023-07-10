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
                'constraints' => [
                    new Regex('/^[a-zA-Z]*$/', 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.'),
                    new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
                ],
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Zone de recherche / localisation* : ',
                'required' => true,
                'constraints' => [
                    new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
                ]
            ])
            ->add('pet_befriends', TextareaType::class, [
                'label' => 'Affinités de votre animal : ',
                'required' => false,
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_health', TextareaType::class, [
                'label' => 'Informations concernant la santé : ',
                'required' => false,
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_caractere', TextareaType::class, [
                'label' => 'Informations sur le caractère ou diverses : ',
                'required' => false,
                'constraints' => [
                    new Regex('/^\w+/', 'Veuillez entrer des mots valides.'),
                ],
            ])
            ->add('pet_age', TextType::class, [
                'label' => 'Âge de votre animal* : ',
                'required' => true,
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
