<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
    public function importationCdv(): Response
    {


        return $this->render('admin/importation.html.twig');
    }

//    /**
//     * @Route("/admin/upload-excel", name="xlsx")
//     */
//    public function xslx(Request $request)
//    {
//        $file = $request->files->get('file');
//
//        $fileFolder = __DIR__ . '/../../assets/upload/csv/';
//
//        $filePathName = 'utilisateurs';
//        try {
//            $file->move($fileFolder, $filePathName);
//        } catch (FileException $e) {
//            dd($e);
//        }
//        $spreadsheet = IOFactory::load($fileFolder . $filePathName);
//        $row = $spreadsheet->getActiveSheet()->removeRow(1);
//        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
//        dd($sheetData);
//
//    }

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
