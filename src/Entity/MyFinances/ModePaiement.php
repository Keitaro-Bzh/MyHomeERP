<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Personne;
use App\Repository\MyFinances\ModePaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=ModePaiementRepository::class)
 * @ORM\Table(name= "myfinances_modesPaiement")
 * @ORM\HasLifecycleCallbacks
 */
class ModePaiement
{
    use suiviLog;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $modePaiement;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="modePaiements")
     * @ORM\JoinColumn(nullable=true)
     */
    private $titulaire;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numeroCarte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $chequeNumeroDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $chequeNumeroFin;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="modePaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="modePaiement")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="mode_paiement")
     * @ORM\Column(name="mode_paiement_echeance_id")
     */
    private $echeances;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
        $this->echeances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getTitulaire(): ?Personne
    {
        return $this->titulaire;
    }

    public function setTitulaire(?Personne $titulaire): self
    {
        $this->titulaire = $titulaire;

        return $this;
    }

    public function getNumeroCarte(): ?int
    {
        return $this->numeroCarte;
    }

    public function setNumeroCarte(?int $numeroCarte): self
    {
        $this->numeroCarte = $numeroCarte;

        return $this;
    }

    public function getChequeNumeroDebut(): ?int
    {
        return $this->chequeNumeroDebut;
    }

    public function setChequeNumeroDebut(?int $chequeNumeroDebut): self
    {
        $this->chequeNumeroDebut = $chequeNumeroDebut;

        return $this;
    }

    public function getChequeNumeroFin(): ?int
    {
        return $this->chequeNumeroFin;
    }

    public function setChequeNumeroFin(?int $chequeNumeroFin): self
    {
        $this->chequeNumeroFin = $chequeNumeroFin;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setModePaiement($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getModePaiement() === $this) {
                $operation->setModePaiement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Echeance[]
     */
    public function getEcheances(): Collection
    {
        return $this->echeances;
    }

    public function addEcheance(Echeance $echeance): self
    {
        if (!$this->echeances->contains($echeance)) {
            $this->echeances[] = $echeance;
            $echeance->setModePaiement($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            // set the owning side to null (unless already changed)
            if ($echeance->getModePaiement() === $this) {
                $echeance->setModePaiement(null);
            }
        }

        return $this;
    }
}
