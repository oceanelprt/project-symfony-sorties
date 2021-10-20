<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortiesByFiltre($filtre, $userId, $isOrganisateur, $isInscrit, $user)
    {
        $qb = $this->createQueryBuilder('s');

        if ($filtre['nom'] != '') {
            $qb->where('s.nom LIKE :key')
                ->setParameter('key', '%' . $filtre['nom'] . '%');
        }

        if ($filtre['ville'] != '') {
            $qb->join('s.lieu', 'l', 'WITH', 'l.ville = :ville')
                ->setParameter('ville', $filtre['ville']);
        }

        if ($filtre['debut'] != null) {
            $qb->andWhere('s.date >= :dateDebut')
                ->setParameter('dateDebut', $filtre['debut']);
        }

        if ($filtre['fin'] != null) {
            $qb->andWhere('s.date >= :dateFin')
                ->setParameter('dateFin', $filtre['fin']);
        }

        if ($isOrganisateur != null) {
            $qb->andWhere('s.createur = :user')
                ->setParameter('user', $userId);
        }

        if ($isInscrit != null) {
            $qb->innerJoin('s.participants', 'P')
                ->andWhere('P.id = :user')
                ->setParameter('user', $userId);
        }


        $qb =$qb->getQuery();

        return $qb->getResult();
    }
}
