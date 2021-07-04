<?php

namespace App\Repository\MyFinances;

use App\Entity\MyFinances\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    public function sqlStatistiqueSoldeParCategorie() {
        /* L'objectif e cette statistique est de récupérer
            la somme des crédits/débits sur les 30 derniers jours
        */
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            select coalesce(round(sum(debit),2),'0.00') as debit,
                coalesce(round(sum(credit),2),'0.00') as credit,
                myfinances_categories.nom as categorie 
            from myfinances_operations, myfinances_sousCategories, myfinances_categories
            where myfinances_operations.categorie_id is not null 
            and virement_id is null
            and type_operation != 'RET'
            and myfinances_operations.categorie_id = myfinances_sousCategories.id
            and myfinances_sousCategories.categorie_id = myfinances_categories.id 
            and date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            and date <= now()
            group by myfinances_categories.nom
            union all
            select coalesce(sum(debit),'0.00') as debit,
                '0.00' as credit,
                'RETRAIT DAB' as categorie 
            from myfinances_operations 
            where type_operation = 'RET'
            and date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            and date <= now()
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return ($stmt->fetchAllAssociative());
    }
}
