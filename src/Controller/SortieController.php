<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sorties")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET", "POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        // recuperation nom utilisateur si connecté
        $user = $this->getUser()->getUserIdentifier();

        // formulaire de filtre des sorties
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('sortie_index'))
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('qb')->orderBy('qb.nom', 'ASC');
                },
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('debut',DateType::class,  [
                'label'    => 'Entre',
                'required'      => false,
                'widget' => "single_text"
            ])
            ->add('fin',DateType::class,  [
                'label'    => 'Et',
                'required'      => false,
                'widget' => "single_text"
            ])
            ->add('isOrganisateur',CheckboxType::class,  [
                'label'    => 'Sorties dont je suis organisateur/trice',
                'required'      => false,
            ])
            ->add('isInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelle je suis inscrit/e',
                'required'      => false,
            ])
            ->add('isNotInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required'      => false,
            ])
            ->add('isPassee',CheckboxType::class,  [
                'label'    => 'Sorties passées',
                'required'      => false,
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();


        if ($request->isMethod('post')) {
            $filtre = $request->request->get('form');
            if($filtre) {
                $utilisateurRepository = $this->getDoctrine()->getRepository(Utilisateur::class);
                $utilisateur = $utilisateurRepository->findOneBy(['pseudo' => $user]);
                $userId = $utilisateur->getId();
                $isOrganisateur = isset($filtre['isOrganisateur']) ? $filtre['isOrganisateur'] : null;
                $isInscrit = isset($filtre['isInscrit']) ? $filtre['isInscrit'] : null;
                $sorties = $sortieRepository->findSortiesByFiltre($filtre, $userId, $isOrganisateur, $isInscrit, $user);
            }
        } else {
            $sorties = $sortieRepository->findAll();
        }
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/creation", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/editer", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
