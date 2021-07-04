<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }
    
    public function findOperationsEcheancesNonRapprocheesAll()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.est_pointe = false')
            ->orWhere('c.est_pointe = 0')
            ->andWhere('c.echeance_operation is not null')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOperationsNonRapprocheesAll()
    {

        return $this->createQueryBuilder('c')
            ->andWhere('c.est_pointe = false')
            ->orWhere('c.est_pointe = 0')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findOperationsNonRapprochees($compte)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.est_pointe = false')
            ->orWhere('c.est_pointe = 0')
            ->andWhere('c.Compte = :compte')
            ->andWhere('c.echeance_operation IS NULL')
            ->setParameter('compte', $compte)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOperationsRapprochees($compte)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.est_pointe = true')
            ->andWhere('c.Compte = :compte')
            ->setParameter('compte', $compte)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findEcheances($compte)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.est_pointe = false')
            ->orWhere('c.est_pointe = 0')
            ->andWhere('c.Compte = :compte')
            ->andWhere('c.echeance_operation IS NOT NULL')
            ->setParameter('compte', $compte)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOperationVirementCredit($virementID): ?Operation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.virementID = :val')
            ->andWhere('o.credit > 0')
            ->setParameter('val', $virementID)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOperationVirementDebit($virementID): ?Operation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.virementID = :val')
            ->andWhere('o.debit > 0')
            ->setParameter('val', $virementID)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
