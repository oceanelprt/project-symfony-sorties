<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class SortieType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de la sortie"
            ])
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
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une ville',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('qb')->orderBy('qb.nom', 'ASC');
                },
                'label' => 'Choisir une ville',
                'required' => false,
                'mapped' => false
            ])
            ->add('nomVille', TextType::class, [
                'label' => "Nom de la ville",
                'required' => false,
                'mapped' => false
            ])
            ->add('codePostal', TextType::class, [
                'label' => "Code postal",
                'required' => false,
                'mapped' => false
            ])
            ->add('nomLieu', TextType::class, [
                'label' => "Nom du lieu",
                'required' => false,
                'mapped' => false
            ])
            ->add('latitude', NumberType::class, [
                'label' => "Latitude",
                'required' => false,
                'mapped' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => "Longitude",
                'required' => false,
                'mapped' => false
            ])
            ->add('rue', TextType::class, [
                'label' => "Rue",
                'required' => false,
                'mapped' => false
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un lieu',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('qb')->orderBy('qb.nom', 'ASC');
                },
            ])
            ->add('choiceVille', HiddenType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('choiceLieu', HiddenType::class, [
                'required' => false,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
