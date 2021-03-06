<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\ImportationCsvType;
use App\Services\ImportationCsv;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use PhpParser\Node\Expr\Yield_;
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
    public function importationCsv(Request $request, EntityManagerInterface $em, ImportationCsv $importationCsv): Response
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
                sleep(3);

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
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-long-arrow-alt-left', 'sortie_index');
        yield MenuItem::section('');

        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Gestion', 'fas fa-tasks')->setSubItems([
            MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Utilisateur::class),
            MenuItem::linkToCrud('Villes', 'fas fa-city', Ville::class),
            MenuItem::linkToCrud('Lieux', 'fas fa-road', Lieu::class),
        ]);
        yield MenuItem::section('CSV');
        yield MenuItem::linkToRoute('Import CSV', 'fas fa-download', 'importation_csv');

    }
}
