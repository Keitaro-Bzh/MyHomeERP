<?php

namespace App\Entity\MyFinances;

use App\Repository\MyFinances\EcheanceOperationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=EcheanceOperationRepository::class)
 * @ORM\Table(name= "myfinances_echeances_operations")
 * @ORM\HasLifecycleCallbacks
 */
class EcheanceOperation
{
    use suiviLog;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date_echeance;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_echeance;

    /**
     * @ORM\ManyToOne(targetEntity=Echeance::class, inversedBy="echeance_operation")
     */
    private $echeance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(\DateTimeInterface $date_echeance): self
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getMontantEcheance(): ?float
    {
        return $this->montant_echeance;
    }

    public function setMontantEcheance(float $montant_echeance): self
    {
        $this->montant_echeance = $montant_echeance;

        return $this;
    }

    public function getEcheance(): ?Echeance
    {
        return $this->echeance;
    }

    public function setEcheance(?Echeance $echeance): self
    {
        $this->echeance = $echeance;

        return $this;
    }
}
