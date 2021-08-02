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

    public function findOperationByEcheance($echeance)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.echeance = :echeance')
            ->setParameter('echeance', $echeance)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOperationByEcheancePeriode($echeance,$dateDebut,$dateFin)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.echeance = :echeance')
            ->andWhere('e.date_echeance >= :dateDebut')
            ->andWhere('e.date_echeance < :dateFin')
            ->setParameter('echeance', $echeance)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOperationByEcheanceDateDebut($echeance,$dateDebut)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.echeance = :echeance')
            ->andWhere('e.date_echeance >= :dateDebut')
            ->setParameter('echeance', $echeance)
            ->setParameter('dateDebut', $dateDebut)
            ->getQuery()
            ->getResult()
        ;
    }
}
