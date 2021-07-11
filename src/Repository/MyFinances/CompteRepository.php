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
        $comptes = $this->createQueryBuilder('c')
            ->andWhere('c.archive is null')
            ->orWhere('c.archive = 0')
            ->orderBy('c.banque', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        foreach ($comptes as $compte) {
            $compte = $this->calculSoldes($compte);
        }

        return $comptes;
    }

    public function calculSoldes($compte)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            select coalesce(sum(credit),'0.00') as credit,
                coalesce(sum(debit),'0.00') as debit,
                compte_id as compte,
                solde_initial,
                est_pointe 
            from myfinances_operations,
                myfinances_comptes 
            where myfinances_operations.compte_id = myfinances_comptes.id
               and myfinances_operations.compte_id = '" . $compte->getId() . "' 
            group by compte_id, solde_initial, est_pointe;
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $statCompte = $stmt->fetchAllAssociative();
        
        for($i = 0; $i < count($statCompte); $i++) {
            if ($statCompte[$i]['est_pointe'] == "1") {
                $compte->setSoldeCours($compte->getSoldeCours() + $statCompte[$i]['credit'] - $statCompte[$i]['debit']);
            }
            else {
                $compte->setSoldeReel($compte->getSoldeReel() + $statCompte[$i]['credit'] - $statCompte[$i]['debit']);
            }
        }

        if (count($statCompte) > 0) {
            $compte->setSoldeCours($statCompte[0]['solde_initial'] + $compte->getSoldeCours());
            $compte->setSoldeCours(round($compte->getSoldeCours(),2));
            $compte->setSoldeReel($compte->getSoldeCours() + $compte->getSoldeReel());
            $compte->setSoldeReel(round($compte->getSoldeReel(),2));
        }

        if (!$compte->getSoldeCours()) $compte->setSoldeCours('0.00');
        if (!$compte->getSoldeReel()) $compte->setSoldeReel('0.00');

        return $compte;
    }
}
