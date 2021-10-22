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

    /**
     * @return Sortie les sorties en fonction des paramêtres du filtre
     */
    public function findSortiesByFiltre($filtre, $userId, $isOrganisateur, $isInscrit, $isNotInscrit, $isPassee, $date)
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
            $qb->andWhere($qb->expr()->gt('s.date',':dateDebut'))
                ->setParameter('dateDebut', $filtre['debut']);
        }
        if ($filtre['fin'] != null) {
            $qb->andWhere($qb->expr()->gt('s.date',':dateFin'))
                ->setParameter('dateFin', $filtre['fin']);
        }
        if ($isOrganisateur != null) {
            $qb->andWhere($qb->expr()->eq('s.createur',':user'))
                ->setParameter('user', $userId);
        }
        if ($isInscrit != $isNotInscrit) {
            $qb->innerJoin('s.participants', 'p');

            if ($isInscrit != null) {
                $qb->andWhere($qb->expr()->eq('p.id',':user'));
            }
            if ($isNotInscrit != null) {
                $qb->andWhere($qb->expr()->notIn('p.id', $userId))
                    ->andWhere($qb->expr()->notIn('s.createur', ':user'));
            }
            $qb->setParameter('user', $userId);
        }

        if ($isPassee != null) {
            $dateLastMonth = clone $date;
            $qb->andWhere('s.date BETWEEN :from AND :to')
                ->setParameter('to', $date)
                ->setParameter('from', $dateLastMonth->modify('-1 month'));

        } else {
            $qb->andWhere($qb->expr()->gt('s.date',':now'))
                ->setParameter('now', $date->modify('-1 month'));
        }

        $qb =$qb->getQuery();

        return $qb->getResult();
    }

    /**
     * @param $date
     * @return Sortie Toutes les sorties jusque 1 mois après leurs réalisation
     */
   public function findByDate($date)
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.date >= :now')
           ->setParameter('now', $date->modify('-1 month'))
           ->orderBy('s.date', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
}

