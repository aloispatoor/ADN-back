<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'événement',
            ])
            ->add('details', TextType::class, [
                'label' => 'Détails',
                'attr' => array(
                    'placeholder' => 'Écrivez le lieu, l\'horaire, etc...'
                )
            ])
            ->add('beginAt', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('endAt', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
