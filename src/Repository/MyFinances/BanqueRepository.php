<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Banque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Banque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banque[]    findAll()
 * @method Banque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banque::class);
    }

    public function findActif()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.archive is null')
            ->orderBy('c.societe', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    // /**
    //  * @return Banque[] Returns an array of Banque objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Banque
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
