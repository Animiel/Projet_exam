<?php

namespace App\Form;

use App\Entity\Motif;
use App\Entity\Annonce;
use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        ])
        ->add('local', TextType::class, [
            'attr' => [
                'placeholder' => 'Rechercher par localisation...'
            ],
            'required' => false,
        ])
        ->add('motif', EntityType::class, [
            'class' => Motif::class,
            'expanded' => true,
            'multiple' => true,
            'required' => false,
        ])
        ->add('genre', ChoiceType::class, [
            'choices' => [
                'Male' => 'Male',
                'Female' => 'Female',
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => false,
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