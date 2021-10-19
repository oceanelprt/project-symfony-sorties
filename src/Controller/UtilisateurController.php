<?php

    namespace App\Controller;

    use App\Entity\Utilisateur;
    use App\Entity\Ville;
    use App\Repository\UtilisateurRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class UtilisateurController extends AbstractController
    {
        /**
         * @Route("/info-utilisateur/{id}", name="info-utilisateur")
         * @param Request $request
         * @return Response
         */
        public function userInformation(Request $request): Response
        {
            $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
            $repoVille = $this->getDoctrine()->getRepository(Ville::class);

            $infoUser = $repoUtilisateur->find($request->get('id'));
            $nomVille = $infoUser->getVille();

            return $this->render('utilisateur/user-information.html.twig', [
                'infoUser' =>$infoUser,
                'nomVille' =>$nomVille,
            ]);
        }
    }