<?php

namespace App\Entity\Traits\MyFinances;

use App\Entity\MyContrats\ContratFacturation;
use App\Entity\MyFinances\EcheanceOperation;
use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\Operation;
use App\Entity\MyFinances\PositionOrdre;
use App\Entity\MyFinances\SousCategorie;
use DateInterval;
use DateTime;

trait OperationTrait
{
    public function setDebitFromEcheanceOperation(EcheanceOperation $echeanceOperation): self
    {
        $this->setOperationFromEcheanceOperation($echeanceOperation);
        $this->setDebit($echeanceOperation->getMontantEcheance());

        return $this;
    }

    public function setCreditFromEcheanceOperation(EcheanceOperation $echeanceOperation): self
    {
        $this->setOperationFromEcheanceOperation($echeanceOperation);
        $this->setCredit($echeanceOperation->getMontantEcheance());

        return $this;
    }

    private function setOperationFromEcheanceOperation (EcheanceOperation $echeanceOperation): self
    {
        $this->setCompte($echeanceOperation->getEcheance()->getCompte());
        $this->setDescription($echeanceOperation->getEcheance()->getDescription());
        $this->setTypeOperation($echeanceOperation->getEcheance()->getTypeOperation());
        $this->setTypeTiers($echeanceOperation->getEcheance()->getTypeTiers());
        $this->setTiersLibelle($echeanceOperation->getEcheance()->getTiersLibelle());
        $this->setPersonne($echeanceOperation->getEcheance()->getTiersPersonne());
        $this->setSociete($echeanceOperation->getEcheance()->getTiersSociete());
        $this->setCategorie($echeanceOperation->getEcheance()->getSousCategorie());
        $this->setEcheanceOperation($echeanceOperation);
        $this->setModePaiementTrigramme($echeanceOperation->getEcheance()->getModePaiementTrigramme());
        $this->setModePaiement($echeanceOperation->getEcheance()->getModePaiement());
        $this->setDate($echeanceOperation->getDateEcheance());
        $this->setEstPointe(0);
        return $this;
    }

    public function setOperationFromPositionOrdre (PositionOrdre $PositionOrdre, SousCategorie $sousCategorie): self
    {
        $this->setCompte($PositionOrdre->getPosition()->getCompte());
        $this->setDate($PositionOrdre->getDate());
        $this->setDescription($PositionOrdre->getNombreTitres() . ' actions à ' . $PositionOrdre->getValeurTitre() . ' €');
        $this->setTypeTiers('S');
        $this->setSociete($PositionOrdre->getPosition()->getSociete());
        if ($PositionOrdre->getTypeMouvement() == 'A') {
            $this->setTypeOperation('DEB');
            $this->setDebit(($PositionOrdre->getValeurTitre() * $PositionOrdre->getNombreTitres()) + $PositionOrdre->getFrais() + $PositionOrdre->getTaxe());
        }
        else {
            $this->setTypeOperation('CRE');
            $this->setCredit(($PositionOrdre->getValeurTitre() * $PositionOrdre->getNombreTitres()) + $PositionOrdre->getFrais() + $PositionOrdre->getTaxe());
        }
        $this->setCategorie($sousCategorie);
        $this->setModePaiementTrigramme('VIR');
        $this->setEstPointe(1);
        return $this;
    }
}