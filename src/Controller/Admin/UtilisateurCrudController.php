<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('pseudo'),
            TextField::new('nom')->hideOnIndex(),
            TextField::new('prenom')->hideOnIndex(),
            TextField::new('telephone')->hideOnIndex(),
            EmailField::new('email'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating(),
            AssociationField::new('ville'),
            BooleanField::new('is_expired')
                ->setLabel('inactif')
                ->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['pseudo' => 'ASC'])
            ->showEntityActionsAsDropdown(false);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT])
            ->addBatchAction(Action::new('Supprimer Utilisateurs')
                ->linkToCrudAction('deleteUtilisateurs')
                ->addCssClass('btn btn-danger')
                ->setIcon('fa fa-user-check'))
            ;
    }

    public function deleteUtilisateurs(BatchActionDto $batchActionDto)
    {
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($batchActionDto->getEntityIds() as $id) {
            $entityManager->getRepository(Utilisateur::class)->deleteUser($id);
        }
            $entityManager->flush();

            return $this->redirect($batchActionDto->getReferrerUrl());

    }
}
