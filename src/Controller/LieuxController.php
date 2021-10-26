<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LieuxController extends AbstractController
{
    /**
     * @Route("listeLieux/{ville}", requirements={"ville":"\d+"},  name="listeLieu")
     */
    public function listeLieux(Ville $ville, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $lieux = $em->getRepository(Lieu::class)->findBy(["ville" => $ville]);
        $jsonLieux = $serializer->serialize($lieux, 'json', ['groups' => ['show-lieux']]);

        return new JsonResponse(json_decode($jsonLieux));
        //return $this->json(['lieux' => $jsonLieux]);
    }
}