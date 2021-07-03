<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Compte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Cast\String_;

/**
 * @method Compte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compte[]    findAll()
 * @method Compte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compte::class);
    }

    public function findActif()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.archive is null')
            ->orWhere('c.archive = 0')
            ->orderBy('c.banque', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

   /* public function calculSolde()
    {
        $rawSql = "SELECT m.id, (SELECT COUNT(i.id) FROM item AS i WHERE i.myclass_id = m.id)
         AS total FROM myclass AS m";
    
        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
    
        dd($stmt->fetchAll());
    } */
}
