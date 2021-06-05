<?php

namespace App\Entity\Traits\MyFinances;

use App\Entity\MyContrats\ContratFacturation;
use App\Entity\MyFinances\EcheanceOperation;
use App\Entity\MyFinances\Operation;
use DateInterval;
use DateTime;

trait EcheanceTrait
{
    private $tabEcheanceOperations;
    private $tabOperations;

    public function getTabEcheanceOperations() : ?array
    {
        return $this->tabEcheanceOperations;
    }
    public function setEcheanceFromContratOperation(ContratFacturation $contratFacturation): self
    {
        // On va peupleur notre entité avec les valeurs de notre contrat
        $this->setCompte($contratFacturation->getCompte());
        $this->setTypeTiers('S');
        $this->setTiersSociete($contratFacturation->getContrat()->getSociete());
        $this->setMontantTotal($contratFacturation->getMontant());
        $this->setSousCategorie($contratFacturation->getContrat()->getCategorie());
        $this->setDateEcheanceOne($contratFacturation->getDateDebut());
        $this->setDateFin($contratFacturation->getDateFin());
        $this->setModePaiementTrigramme('PRE');
        $this->setDescription($contratFacturation->getContrat()->getDescription());
        $this->setFrequencePaiement($contratFacturation->getFrequencePaiement());
        $this->setFrequenceNombrePaiement($contratFacturation->getFrequenceNombrePaiement());
        $this->setRecalculOperationAnterieur($contratFacturation->getRecalculOperationAnterieur());
        $this->calculNombreEcheanceOperation(); 
 
        $this->calculTableEcheanceOperation();

        return $this;
    }

    /* Dans cette fonction, on va calculer le nombre d'échéance afin de renseigner l'information dans l'entité
        on va également renseigner deux informations pour la génération des échéances-opérations et des opérations
        éventuelles. 
    */
    private function calculNombreEcheanceOperation(): self 
    {  

        if (($this->getDateFin())) {
            $nombreEcheance = 1;

            $dateEcheance = date_create(date_format($this->getDateEcheanceOne(),'d/m/Y')); // Date référence au 01/01/2021 par exemple
            $dateEcheanceDiff = $dateEcheance;
            while ($dateEcheance <= $this->getDateFin()) {
                    $dateEcheance = $dateEcheance->add(new DateInterval("P" . $this->getFrequenceNombrePaiement() . $this->getFrequencePaiement()));

                // Particularité pour le calcul du nombre de mois pour les jours 29,30,31
                // qui calcule la date d'échéance sur le mois suivant.                
                if($dateEcheanceDiff->format("d") != $dateEcheance->format("d")) {
                    $dateEcheance->sub(new DateInterval("P" . $dateEcheance->format("d") . "D"));
                }   

                if ($dateEcheance <= $this->getDateFin()) {
                    $nombreEcheance++;
                }
            }
            
            $this->setNombreEcheances($nombreEcheance);
        }
        else {
            $this->setNombreEcheances(0);
        }
        return $this;
    }

    private function calculTableEcheanceOperation(): self 
    {
        $numEcheance = 1;
        $montantTotalEcheance = 0;
        if ($this->getNombreEcheances() == 0) {
            $date_fin_boucle = new DateTime('last day of this month');
            
        }
        else {
            $date_fin_boucle = $this->getDateFin();
        }
        if (($this->getNombreEcheances() > 0 ) || ($this->getNombreEcheances() == 0 && $this->getDateEcheanceOne() <= new DateTime('last day of this month'))) {
        //if ($this->getDateEcheanceOne() <= new DateTime('last day of this month')) {
            $dateEcheance = date_create(date_format($this->getDateEcheanceOne(),'d/m/Y')); 
            $dateEcheanceDiff = $dateEcheance;  
            do {         
                // Creation de notre échéance application
                $echeanceOperation = new EcheanceOperation();

                $echeanceOperation->setDateEcheance(date_create(date_format($dateEcheance,'d/m/Y')));
                $echeanceOperation->setEcheance($this);

                // On va calculer le montant de l'échéance en fonction du nombre d'échéance
                // Mais dans le cas de plusieurs échéances, on va calculer la dernière échéance
                // qui soldera le montant total
                if ($this->getNombreEcheances() == 0) {
                    $echeanceOperation->setMontantEcheance($this->getMontantTotal());
                    
                }
                else {
                    if ($numEcheance < $this->getNombreEcheances()) {
                        $montantTotalEcheance = $montantTotalEcheance + round($this->getMontantTotal()/$this->getNombreEcheances(),2,PHP_ROUND_HALF_UP);
                        $echeanceOperation->setMontantEcheance(round($this->getMontantTotal()/$this->getNombreEcheances(),2,PHP_ROUND_HALF_UP));
                    }
                    else {
                        $echeanceOperation->setMontantEcheance($this->getMontantTotal() - $montantTotalEcheance);
                    }
                }

                // On va également créer un tableau d'opération pour un recalcul des échéances passées
                if($this->getRecalculOperationAnterieur) {
                    $operation = new Operation();
                    $operation->setDate($echeanceOperation->getDateEcheance());
                }

                // On peuple nos tableaux
                $this->tabEcheanceOperations[$numEcheance] = $echeanceOperation;
                $this->tabOperations[$numEcheance] = $this->getRecalculOperationAnterieur ? $operation : null;

                // On passe à l'échéance suivante
                $numEcheance++;
                $dateEcheance = $dateEcheance->add(new DateInterval("P" . $this->getFrequenceNombrePaiement() . $this->getFrequencePaiement()));
                // Particularité pour le calcul du nombre de mois pour les jours 29,30,31
                // qui calcule la date d'échéance sur le mois suivant.                
                if($dateEcheanceDiff->format("d") != $dateEcheance->format("d")) {
                    $dateEcheance->sub(new DateInterval("P" . $dateEcheance->format("d") . "D"));
                }
            } while ($dateEcheance <= $date_fin_boucle);
        }
        else {
            // On est dans le cas ou il s'agit d'un contrat permanent débutant après le mois actuel
            // On ne va donc créer qu'une seule EcheanceOperation
            $echeanceOperation = new EcheanceOperation();

            $echeanceOperation->setDateEcheance(date_create(date_format($this->getDateEcheanceONe,'d/m/Y')));
            $echeanceOperation->setEcheance($this);
            $echeanceOperation->setMontantEcheance($this->getMontantTotal());

            $this->tabEcheanceOperations[1] = $echeanceOperation;
            // On ne crée aucune opération
            $this->tabOperations[1] =  null;
        }
        return $this;
    }

}