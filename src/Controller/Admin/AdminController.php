<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\ImportationCsvType;
use App\Services\ImportationCsv;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * @Route("/admin/importation", name="importation_csv")
     */
    public function importationCdv(Request $request, EntityManagerInterface $em, ImportationCsv $importationCsv): Response
    {
        $isPost = false;
        $utilisateursExistants = new ArrayCollection();

        $form = $this->createForm(ImportationCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isPost = true;
            $importationFile = $form->get('importation')->getData();
            if ($importationFile){
                $importationCsv->upload($importationFile);

                $reader = $importationCsv->createReader();

                foreach ($reader as $row) {
                    if (!$importationCsv->addUserAndCity($row)){
                        $utilisateur = $importationCsv->invalidUser($row);
                        if (!$utilisateursExistants->contains($utilisateur)){
                            $utilisateursExistants->add($utilisateur);
                        }
                    }
                }
                $em->flush();
            }
        }

        return $this->renderForm('admin/importation.html.twig', [
            'form' => $form,
            'utilisateursExistants' => $utilisateursExistants,
            'isPost' => $isPost,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sorties.com');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Utilisateur::class);
        yield MenuItem::linkToRoute('Import CSV', 'fas fa-download', 'importation_csv');
    }
}
