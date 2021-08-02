<?php

namespace App\Repository\MyContrats;

use App\Entity\MyContrats\ContratFacturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContratFacturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContratFacturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContratFacturation[]    findAll()
 * @method ContratFacturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratFacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContratFacturation::class);
    }

    public function findAllOrderDateDebut($contrat)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.Contrat = :param')
            ->orderBy('c.date_debut', 'desc')
            ->setParameter('param', $contrat)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findContratFacturationByEcheance($echeance): ?ContratFacturation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Echeance = :param')
            ->setParameter('param', $echeance)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    // /**
    //  * @return ContratFacturation[] Returns an array of ContratFacturation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContratFacturation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
