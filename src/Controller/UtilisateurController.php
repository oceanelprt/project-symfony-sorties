<?php

    namespace App\Controller;

    use App\Entity\Utilisateur;
    use App\Entity\Ville;
    use App\Form\RegistrationFormType;
    use App\Form\UtilisateurType;
    use App\Repository\UtilisateurRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;
    /**
    @Route("/utilisateur", name="utilisateur_")
    */
    class UtilisateurController extends AbstractController
    {
        /**
         * @Route("/{utilisateur}", requirements={"utilisateur"="\d+"}, name="info")
         * @param Request $request
         * @return Response
         */
        public function showUser(Request $request, Utilisateur $utilisateur):Response
        {
            return $this->render('utilisateur/user-information.html.twig', [
                'utilisateur' => $utilisateur
            ]);
        }

        /**
         * @Route("/{utilisateur}/edit", requirements={"utilisateur"="\d+"}, name="edit")
         * @param Request $request
         * @param Utilisateur $utilisateur
         */
        public function editUser(Request $request, Utilisateur $utilisateur, UserPasswordHasherInterface $userPasswordHasherInterface)
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

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('home');            }

            return $this->render('utilisateur/user-edit.html.twig', [
                'form' => $form->createView()
            ]);

        }
    }