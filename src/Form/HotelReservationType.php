<?php

namespace App\Form;

use App\Entity\HotelReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('Lastname')
            ->add('PhoneNumber')
            ->add('HowManyAdultPeople')
            ->add('HowManyKids')
            ->add('DateFrom')
            ->add('DateTo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HotelReservation::class,
        ]);
    }
}
