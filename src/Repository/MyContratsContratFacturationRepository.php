<?php

namespace App\Repository;

use App\Entity\MyContratsContratFacturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MyContratsContratFacturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyContratsContratFacturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyContratsContratFacturation[]    findAll()
 * @method MyContratsContratFacturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyContratsContratFacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyContratsContratFacturation::class);
    }

    // /**
    //  * @return MyContratsContratFacturation[] Returns an array of MyContratsContratFacturation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MyContratsContratFacturation
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
