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

trait PositionTrait
{
    private $nombreActions;
    private $pru;
    private $cours;
    private $montantAchat;
    private $montantCours;

    public function getNombreActions(): ?int
    {
        $ordres = $this->getPositionOrdres()->toArray();
        $nbActions = 0;

        if (count($ordres) > 0) {
            for ($i = 0; $i < count($ordres); $i++) {
                $nbActions = $nbActions + $ordres[$i]->getNombreTitres();
            }
        }

        $this->setNombreActions($nbActions);
        
        return $this->nombreActions;
    }

    public function setNombreActions(?string $nombreActions): self
    {
        $this->nombreActions = $nombreActions;

        return $this;
    }

    public function getPru(): ?float
    {
        $this->setPru(round($this->getMontantAchat() / $this->getNombreActions(),2));

        return $this->pru;
    }

    public function setPru(?string $pru): self
    {
        $this->pru = $pru;

        return $this;
    }
    
    public function getCours(): ?float
    {
        return $this->cours;
    }

    public function setCours(?string $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getMontantAchat(): ?float
    {
        $ordres = $this->getPositionOrdres()->toArray();
        $montantTotal = 0;

        if (count($ordres) > 0) {
            for ($i = 0; $i < count($ordres); $i++) {
                $montantTotal = $montantTotal + ($ordres[$i]->getNombreTitres() * $ordres[$i]->getValeurTitre() + $ordres[$i]->getFrais() + $ordres[$i]->getTaxe());
            }
        }
        
        $this->setMontantAchat(round($montantTotal,2));
        
        return $this->montantAchat;
    }

    public function setMontantAchat(?string $montantAchat): self
    {
        $this->montantAchat = $montantAchat;

        return $this;
    }

    public function getMontantCours(): ?float
    {
        return $this->montantCours;
    }

    public function setMontantCuors(?string $montantCours): self
    {
        $this->montantCours = $montantCours;

        return $this;
    }
}