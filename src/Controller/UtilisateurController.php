<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
    public function editUser(Utilisateur $utilisateur, Request $request, FileUploader $fileUploader,
                             UserPasswordHasherInterface $userPasswordHasherInterface): Response
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

            if ($photo) {
                $photoFileName = $fileUploader->upload($photo);
                $utilisateur->setPhoto($photoFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('utilisateur_edit', ['utilisateur' => $utilisateur->getId()]);
        }

        return $this->render('utilisateur/user-edit.html.twig', [
            'form' => $form->createView(),
            'photo' => 'photos/' . $utilisateur->getPhoto()
        ]);
    }
}
