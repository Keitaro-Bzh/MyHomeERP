<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Echeance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Echeance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echeance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echeance[]    findAll()
 * @method Echeance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcheanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echeance::class);
    }

    public function findActif()
    {
        $contrats = $this->createQueryBuilder('c')
            ->andWhere('c.date_fin >= :now')
            ->orWhere('c.date_fin is null')
            ->orWhere('c.est_solde = false')
            ->setParameter('now',new \DateTime('now'))
            ->orderBy('c.date_echeance_one', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $contrats;
    }
    // /**
    //  * @return Echeance[] Returns an array of Echeance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Echeance
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
