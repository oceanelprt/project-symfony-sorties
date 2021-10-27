<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET", "POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        // recuperation nom utilisateur si connecté
        $user = $this->getUser()->getUserIdentifier();

        // formulaire de filtre des sorties
        $form = $this->createForm(FiltreType::class);
        $form->handleRequest($request);
        $date = new \DateTime();
        if ($form->isSubmitted() && $form->isValid()) {
            $filtre = $request->request->get('filtre');
            if($filtre) {
                // info sur l'utilisateur connécté
                $utilisateurRepository = $this->getDoctrine()->getRepository(Utilisateur::class);
                $utilisateur = $utilisateurRepository->findOneBy(['pseudo' => $user]);

                // information pour le filtre après transformation
                $userId = $utilisateur->getId();
                $isOrganisateur = $filtre['isOrganisateur'] ?? null;
                $isInscrit = $filtre['isInscrit'] ?? null;
                $isNotInscrit = $filtre['isNotInscrit'] ?? null;
                $isPassee = $filtre['isPassee'] ?? null;
                $sorties = $sortieRepository->findSortiesByFiltre($filtre, $userId, $isOrganisateur, $isInscrit, $isNotInscrit, $isPassee, $date);
            }
        } else {
            $sorties = $sortieRepository->findNonArchivees($date);
        }

        if ($request->request->get('motifAnnulation')) {
            $idSortie = $request->request->get('idSortie');
            $motifAnnulation = $request->request->get('motifAnnulation');
            $sortie = $em->getRepository(Sortie::class)->find($idSortie);
            $etat = $em->getRepository(Etat::class)->findOneBy(['etat' => Etat::ETAT_ANNULE]);
            $sortie->setEtat($etat);
            $sortie->setMotifAnnulation($motifAnnulation);

            $em->persist($sortie);
            $em->flush();
        }

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("sortie/creation", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $villeRepository = $this->getDoctrine()->getRepository(Ville::class);
        $lieuRepository = $this->getDoctrine()->getRepository(Lieu::class);

        $etat = $em->getRepository(Etat::class)->findOneBy(['etat' => Etat::ETAT_EN_CREATION]);

        $lieu = new Lieu();
        $ville = new Ville();
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        $data = $request->request->get('sortie');

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etat);
            $sortie->setCreateur($this->getUser());

            //Si création d'une ville
            if ($data['choiceVille'] === "choiceNewVille") {
                $ville->setCodePostal($data['codePostal']);
                $ville->setNom($data['nomVille']);

                $em->persist($ville);
            } else { //Si choix d'une ville
                $ville = $villeRepository->find($data['ville']);
            }

            //Si création d'un lieu
            if ($data['choiceLieu'] === "choiceNewLieu") {
                $lieu->setNom($data['nomLieu']);
                $lieu->setLatitude($data['latitude']);
                $lieu->setLatitude($data['latitude']);
                $lieu->setLongitude($data['longitude']);
                $lieu->setRue($data['rue']);
                $lieu->setVille($ville);

                $em->persist($ville);
            } else { //Si choix d'un lieu
                $lieu = $lieuRepository->find($data['lieu']);
            }

            $sortie->setLieu($lieu);

            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("sortie/{sortie}", requirements={"sortie"="\d+"}, name="sortie_show", methods={"GET", "POST"})
     */
    public function show(Sortie $sortie, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->request->get('motifAnnulation')) {
            $motifAnnulation = $request->request->get('motifAnnulation');
            $etat = $em->getRepository(Etat::class)->findOneBy(['etat' => Etat::ETAT_ANNULE]);
            $sortie->setEtat($etat);
            $sortie->setMotifAnnulation($motifAnnulation);

            $em->persist($sortie);
            $em->flush();
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("sortie/{sortie}/editer", requirements={"sortie"="\d+"}, name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lieuRepository = $em->getRepository(Lieu::class);
        $villeRepository = $em->getRepository(Ville::class);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        $lieu = new Lieu();
        $ville = new Ville();

        $data = $request->request->get('sortie');

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setCreateur($this->getUser());

            //Si création d'une ville
            if ($data['choiceVille'] === "choiceNewVille") {
                $ville->setCodePostal($data['codePostal']);
                $ville->setNom($data['nomVille']);

                $em->persist($ville);
            } else { //Si choix d'une ville
                $ville = $villeRepository->find($data['ville']);
            }

            //Si création d'un lieu
            if ($data['choiceLieu'] === "choiceNewLieu") {
                $lieu->setNom($data['nomLieu']);
                $lieu->setLatitude($data['latitude']);
                $lieu->setLatitude($data['latitude']);
                $lieu->setLongitude($data['longitude']);
                $lieu->setRue($data['rue']);
                $lieu->setVille($ville);

                $em->persist($ville);
            } else { //Si choix d'un lieu
                $lieu = $lieuRepository->find($data['lieu']);
            }

            $sortie->setLieu($lieu);

            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("sortie/{sortie}/register", requirements={"sortie"="\d+"}, name="sortie_register")
     */
    public function register(Request $request, Sortie $sortie)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['pseudo' => $user->getUserIdentifier()]);

        $sortie->addParticipant($utilisateur);

        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @Route("sortie/{sortie}/unregister", requirements={"sortie"="\d+"}, name="sortie_unregister")
     */
    public function unregister(Request $request, Sortie $sortie)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['pseudo' => $user->getUserIdentifier()]);

        $sortie->removeParticipant($utilisateur);

        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @Route("sortie/{sortie}/publier", requirements={"sortie"="\d+"}, name="sortie_publier")
     */
    public function publier(Request $request, Sortie $sortie)
    {
        $em = $this->getDoctrine()->getManager();
        $etat = $em->getRepository(Etat::class)->findOneBy(['etat' => Etat::ETAT_OUVERT]);

        $sortie->setEtat($etat);

        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_index');
    }
}
