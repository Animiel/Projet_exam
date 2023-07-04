<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('infoUne', TextType::class, [
                'label' => 'Première information* : ',
            ])
            ->add('infoDeux', TextType::class, [
                "required" => false,
                'label' => 'Deuxième information : ',
            ])
            ->add('infoTrois', TextType::class, [
                "required" => false,
                'label' => 'Troisième information : ',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de votre animal* : ',
            ])
            ->add('pet_name', TextType::class, [
                'label' => 'Nom de votre animal* : ',
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Zone de recherche / localisation* : ',
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
