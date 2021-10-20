<?php

    namespace App\Controller;

    use App\Entity\Utilisateur;
    use App\Entity\Ville;
    use App\Repository\UtilisateurRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
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
    }