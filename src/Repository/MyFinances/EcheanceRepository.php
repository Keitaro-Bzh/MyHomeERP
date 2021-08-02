<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Echeance;
use DateTime;
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

    public function findActifs()
    {
        $echeances = $this->createQueryBuilder('c')
            ->where('(c.est_solde = false OR c.est_solde is NULL ) AND
                (c.date_fin >= :now OR c.date_fin is null)')
            ->setParameter('now', new DateTime('Now - 1 day'))
            ->orderBy('c.nombre_echeances', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $echeances;
    }

    public function findOldMois()
    {
        $echeances = $this->createQueryBuilder('c')
            ->andWhere('c.date_fin < :dateDebutMois')
            ->setParameter('dateDebutMois',new \DateTime(date('Y-m-01')))
            ->orderBy('c.Compte', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        return $echeances;
    }

    public function findActifsPeriode($dateDebut, $dateFin)
    {
        $echeances = $this->createQueryBuilder('c')
            ->Where('(c.est_solde = false OR c.est_solde is NULL ) AND
                (c.date_fin >= :dateDebut OR c.date_fin is null) AND
                (c.date_echeance_one < :dateFin)')
            ->setParameter('dateFin',$dateFin)
            ->setParameter('dateDebut',$dateDebut)
            ->getQuery()
            ->getResult()
        ;

        return $echeances;
    }

    public function findActifsByDateFin($dateDebut)
    {
        $echeances = $this->createQueryBuilder('c')
            ->Where('(c.est_solde = false OR c.est_solde is NULL )')
            ->andWhere('c.date_fin < :dateDebut')
            ->setParameter('dateDebut',$dateDebut)
            ->getQuery()
            ->getResult()
        ;

        return $echeances;
    }

    public function findActifsDateFinNull()
    {
        $echeances = $this->createQueryBuilder('c')
            ->Where('(c.est_solde = false OR c.est_solde is NULL )')
            ->andWhere('c.date_fin IS NULL')
            ->andWhere('c.nombre_echeances > 0')
            ->getQuery()
            ->getResult()
        ;

        return $echeances;
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
