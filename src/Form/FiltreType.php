<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('qb')->orderBy('qb.nom', 'ASC');
                },
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('debut',DateType::class,  [
                'label'    => 'Entre',
                'required'      => false,
                'widget' => "single_text"
            ])
            ->add('fin',DateType::class,  [
                'label'    => 'Et',
                'required'      => false,
                'widget' => "single_text"
            ])
            ->add('isOrganisateur',CheckboxType::class,  [
                'label'    => 'Sorties dont je suis organisateur/trice',
                'required'      => false,
            ])
            ->add('isInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelle je suis inscrit/e',
                'required'      => false,
            ])
            ->add('isNotInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required'      => false,
            ])
            ->add('isPassee',CheckboxType::class,  [
                'label'    => 'Sorties passées',
                'required'      => false,
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }
}
