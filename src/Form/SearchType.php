<?php

namespace App\Form;

use App\Entity\Motif;
use App\Entity\Annonce;
use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('q', TextType::class, [
            'attr' => [
                'placeholder' => 'Rechercher par nom d\'animal...',
            ],
            'required' => false,
            'constraints' => [
                new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
            ],
        ])
        // ->add('local', TextType::class, [
        //     'attr' => [
        //         'placeholder' => 'Rechercher par localisation...'
        //     ],
        //     'required' => false,
        //     'constraints' => [
        //         new Regex('/^[a-zA-Z]*$/', 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.'),
        //         new Length(['min' => 2, 'minMessage' => 'Minimum 2 caractères requis.']),
        //     ],
        // ])
        ->add('motif', EntityType::class, [
            'class' => Motif::class,
            'expanded' => true,
            'multiple' => true,
            'required' => false,
        ])
        ->add('genre', ChoiceType::class, [
            'choices' => [
                'Mâle' => 'Mâle',
                'Femelle' => 'Femelle',
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'placeholder' => 'Aucun',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}



?>