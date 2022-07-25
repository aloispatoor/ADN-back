<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('pronouns', TextType::class)
            ->add('city', TextType::class)
            ->add('genders', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        'Non spécifié' => null,
                        'Homme' => 'Homme',
                        'Femme' => 'Femme',
                        'Homme cis' => "Homme cis",
                        'Femme cis' => "Femme cis",
                        'Homme trans' => 'Homme trans',
                        'Femme trans' => 'Femme trans',
                        'Non Binaire' => 'Non Binaire',
                        'Non Binaire Transféminine' => 'Non Binaire Transféminine',
                        'Non Binaire Transmasculin' => 'Non Binaire Transmasculin',
                        'Genre Fluide' => 'Genre Fluide',
                        'Genre Neutre' => 'Genre Neutre',
                        'Agenre' => 'Agenre',
                        'Genre Queer' => 'Genre Queer'
                    ],
                ],
            ])
            ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_label' => false,
                'asset_helper' => true,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
