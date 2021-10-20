<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [new Length([
                    'min' => 6,
                    'minMessage' => 'Le nouveau mot de passe doit faire au moins 6 caractères.',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ])]
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('ville', EntityType::class, ['class' => Ville::class, 'choice_label' => 'nom',
                    'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('qb')->orderBy('qb.nom', 'ASC');
                },
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
