<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/utilisateur", name="utilisateur_")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/{utilisateur}", requirements={"utilisateur"="\d+"}, name="info")
     */
    public function showUser(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/user-information.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    /**
     * @Route("/{utilisateur}/edit", requirements={"utilisateur"="\d+"}, name="edit")
     * @Security("is_granted('utilisateur_edit', utilisateur)")
     */
    public function editUser(Utilisateur $utilisateur, Request $request,
                             UserPasswordHasherInterface $userPasswordHasherInterface, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if ($form->get('password')->getData()) {
                $utilisateur->setPassword(
                    $userPasswordHasherInterface->hashPassword(
                        $utilisateur,
                        $form->get('password')->getData()
                    )
                );
            }

            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $utilisateur->setPhoto($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('utilisateur_edit', ['utilisateur' => $utilisateur->getId()]);
        }

        return $this->render('utilisateur/user-edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
