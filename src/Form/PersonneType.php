<?php

namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'NOM :',
                'attr' => ['placeholder' => 'Renseignez votre nom de famille']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'attr' => ['placeholder' => 'Renseignez votre prénom']
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance : ',
                'widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
