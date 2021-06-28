<?php

namespace App\Repository\MyContacts;

use App\Entity\MyContacts\Societe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Societe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Societe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Societe[]    findAll()
 * @method Societe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Societe::class);
    }

    public function findActif()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.archive is null')
            ->orWhere('c.archive = 0')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBanque()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.archive is null')
            ->orWhere('c.archive = 0')
            ->andWhere('c.estBanque = true')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
