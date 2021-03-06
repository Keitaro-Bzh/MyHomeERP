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
        $this->setMontantFraction($contratFacturation->getMontantFraction());
        $this->setTypeTiers('S');
        $this->setTiersSociete($contratFacturation->getContrat()->getSociete());
        $this->setMontantTotal($contratFacturation->getMontant());
        $this->setSousCategorie($contratFacturation->getContrat()->getCategorie());
        $this->setDateEcheanceOne($contratFacturation->getDateDebut());
        $this->setDateFin($contratFacturation->getDateFin());
        switch ($contratFacturation->getTypeMouvement()) {
            case 'D':
                $this->setModePaiementTrigramme('PRE');
                $this->setTypeOperation('DEB');
                break;
            case 'C':
                $this->setModePaiementTrigramme('VIR');
                $this->setTypeOperation('CRE');
                break;
        }
        
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
    public function calculNombreEcheanceOperation(): self 
    {  
        // On va faire une règle pour aller calculer le nombre d'échéance dans le cas d'un contrat
        if ($this->getNombreEcheances() == 0) {
            if ($this->getDateFin()) {
                $nombreEcheance = 1;

                $dateEcheance = clone($this->getDateEcheanceOne()); // Date référence au 01/01/2021 par exemple
                $dateEcheanceDiff = clone($dateEcheance);
                while ($dateEcheance <= $this->getDateFin()) {
                    $dateEcheance = $dateEcheance->add(new DateInterval("P" . $this->getFrequenceNombrePaiement() . $this->getFrequencePaiement()));

                    // Particularité pour le calcul du nombre de mois pour les jours 29,30,31
                    // qui calcule la date d'échéance sur le mois suivant.                
                    if($dateEcheanceDiff->format("d") != $dateEcheance->format("d") &&  $this->getFrequencePaiement() == 'M') {
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
        } else {
            if ($this->getNombreEcheances() == 1) {
                $this->setDateFin(clone($this->getDateEcheanceOne()));
            }
            else {
                // On est dans le cas ou nous sommes dans un calcul d'échéance avec un paiement multiple
                $dateEcheance = clone($this->getDateEcheanceOne()); // Date référence au 01/01/2021 par exemple
                $dateEcheanceDiff = clone($dateEcheance);
                $dateEcheance = $dateEcheance->add(new DateInterval("P" . ($this->getNombreEcheances() - 1) . $this->getFrequencePaiement()));

                // Particularité pour le calcul du nombre de mois pour les jours 29,30,31
                // qui calcule la date d'échéance sur le mois suivant.                
                if($dateEcheanceDiff->format("d") != $dateEcheance->format("d") &&  $this->getFrequencePaiement() == 'M') {
                    $dateEcheance->sub(new DateInterval("P" . $dateEcheance->format("d") . "D"));
                }
                $this->setDateFin($dateEcheance);
            }
        }
        return $this;
    }

    public function calculTableEcheanceOperation(): self 
    {
        $numEcheance = 1;
        $montantTotalEcheance = 0;
        $dateFinMoisCours = new DateTime('last day of this month');

        if ($this->getNombreEcheances() == 0) {
            // On prend en compte les périodes passées dans le cas ou l'on veut recalculer les échéances passées
            if ($this->getDateFin()) {
                $date_fin_boucle = clone($this->getDateFin());
            }
            else {
                $date_fin_boucle = new DateTime('last day of this month');
            }
        }
        else {
            $date_fin_boucle = clone($this->getDateFin());
        }
        $dateEcheanceOne = clone($this->getDateEcheanceOne()) ;
        $dateJourEcheanceOne = $dateEcheanceOne->format('d');


        if (($this->getNombreEcheances() > 0 ) || ($this->getNombreEcheances() == 0 && $this->getDateEcheanceOne() <= $dateFinMoisCours)) {
            // On va gérer une particularité sur la date dans le cas ou on ne demande pas le recalcul pour une echéance permanente
            if ($this->getNombreEcheances() == 0 && ($this->getRecalculOperationAnterieur() == false || $this->getRecalculOperationAnterieur() == 0)) {
                if ($dateEcheanceOne <= new DateTime(date("Y-m-01"))) {
                    if ($dateEcheanceOne->format('d') > $dateFinMoisCours->format('d')){
                        $dateEcheanceOne = clone($dateFinMoisCours);
                    }
                    else {
                        $dateEcheanceOne = new DateTime($dateFinMoisCours->format('Y').'-'.$dateFinMoisCours->format('m').'-'.$dateEcheanceOne->format('d'));
                    }
                }
            }

            // On ne va créer notre tableau d'échéance opération uniquement
            // si la première échéance est sur le mois en cours
            do {         
                // Creation de notre échéance application
                $echeanceOperation = new EcheanceOperation();

                $echeanceOperation->setDateEcheance(clone($dateEcheanceOne));
                $echeanceOperation->setEcheance($this);

                // On va calculer le montant de l'échéance en fonction du nombre d'échéance
                // Mais dans le cas de plusieurs échéances, on va calculer la dernière échéance
                // qui soldera le montant total
                if ($this->getNombreEcheances() == 0) {
                    $echeanceOperation->setMontantEcheance($this->getMontantTotal());
                }
                else {
                    if ($this->getNombreEcheances() > 0 && $this->getMontantFraction() == true) {
                        if ($numEcheance < $this->getNombreEcheances()) {
                            $montantTotalEcheance = $montantTotalEcheance + round($this->getMontantTotal()/$this->getNombreEcheances(),2,PHP_ROUND_HALF_UP);
                            $echeanceOperation->setMontantEcheance(round($this->getMontantTotal()/$this->getNombreEcheances(),2,PHP_ROUND_HALF_UP));
                        }
                        else {
                            $echeanceOperation->setMontantEcheance($this->getMontantTotal() - $montantTotalEcheance);
                        }
                    }
                    else {
                        $echeanceOperation->setMontantEcheance($this->getMontantTotal());
                    }
                }
                // On peuple nos tableaux
                $this->tabEcheanceOperations[$numEcheance] = $echeanceOperation;

                // On passe à l'échéance suivante
                $numEcheance++;
                
                // Particularité pour le calcul du nombre de mois pour les jours 29,30,31
                // qui calcule la date d'échéance sur le mois suivant.  
                if ($dateJourEcheanceOne > 28) {
                    if ($dateEcheanceOne->format('m')+1 <= 12) {
                        $dateEcheanceOne = new DateTime($dateEcheanceOne->format('Y').'-'.($dateEcheanceOne->format('m')+1).'-28');
                    }
                    else {
                        $dateEcheanceOne = new DateTime(($dateEcheanceOne->format('Y')+1).'-01-28');
                    }
                    
                }
                else {
                    $dateEcheanceOne = $dateEcheanceOne->add(new DateInterval("P" . $this->getFrequenceNombrePaiement() . $this->getFrequencePaiement()));
                }
            } while ($dateEcheanceOne <= $date_fin_boucle);
        }
        else {
            // On est dans le cas ou il s'agit d'un contrat permanent débutant après le mois actuel
            // On ne va donc créer aucune entrée. Celle-ci sera calculé en temps et en heure par
            // un traitement automatique
        }
        return $this;
    }
}