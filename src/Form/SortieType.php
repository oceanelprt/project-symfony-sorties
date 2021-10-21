<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                    'label' => "Nom de la sortie"
                ]
    )
            ->add('duree', IntegerType::class, [
                'constraints' => [new Positive()],
                'label' => "Durée de la sortie"
            ])
            ->add('date',DateTimeType::class,  [
                'required' => true,
                'widget' => "single_text",
                'label' => "Date et heure de la sortie"
            ])

            ->add('dateCloture',DateType::class,  [
                'required' => true,
                'widget' => "single_text",
                'attr'   => ['min' => 'date',
                'label' => "Date limite d'inscription à la sortie"
            ]])
            ->add('nombrePlaces', IntegerType::class, [
                'constraints' => [new Positive()],
                'label' => "Nombre de places disponibles"
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description de la sortie"
            ])
            ->add('lieu')
            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
