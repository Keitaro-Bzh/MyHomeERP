<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\PositionOrdre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PositionOrdre|null find($id, $lockMode = null, $lockVersion = null)
 * @method PositionOrdre|null findOneBy(array $criteria, array $orderBy = null)
 * @method PositionOrdre[]    findAll()
 * @method PositionOrdre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionOrdreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionOrdre::class);
    }

    // /**
    //  * @return PositionOrdre[] Returns an array of PositionOrdre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PositionOrdre
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
