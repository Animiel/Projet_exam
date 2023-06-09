<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'required' => false,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom : ',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email* : ',
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro de téléphone : ',
                'required' => false,
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet* : ',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message* : ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
