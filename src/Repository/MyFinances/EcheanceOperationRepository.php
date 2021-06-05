<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\EcheanceOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EcheanceOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcheanceOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcheanceOperation[]    findAll()
 * @method EcheanceOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcheanceOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcheanceOperation::class);
    }

    // /**
    //  * @return EcheanceOperation[] Returns an array of EcheanceOperation objects
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
    public function findOneBySomeField($value): ?EcheanceOperation
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
