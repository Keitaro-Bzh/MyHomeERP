<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Societe;
use App\Repository\MyFinances\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;
use App\Entity\Traits\MyFinances\PositionTrait;

/**
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 * @ORM\Table(name= "myfinances_positions")
 * @ORM\HasLifecycleCallbacks
 */
class Position
{
    use suiviLog;
    use PositionTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="positions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $abrege_societe;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity=PositionOrdre::class, mappedBy="Position", orphanRemoval=true)
     */
    private $PositionOrdres;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $est_solde;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="positions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    public function __construct()
    {
        $this->PositionOrdres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getAbregeSociete(): ?string
    {
        return $this->abrege_societe;
    }

    public function setAbregeSociete(?string $abrege_societe): self
    {
        $this->abrege_societe = $abrege_societe;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|PositionOrdres[]
     */
    public function getPositionOrdres(): Collection
    {
        return $this->PositionOrdres;
    }

    public function addPositionOrdre(PositionOrdre $PositionOrdre): self
    {
        if (!$this->PositionOrdres->contains($PositionOrdre)) {
            $this->PositionOrdres[] = $PositionOrdre;
            $PositionOrdre->setPosition($this);
        }

        return $this;
    }

    public function removePositionOrdre(PositionOrdre $PositionOrdre): self
    {
        if ($this->PositionOrdres->removeElement($PositionOrdre)) {
            // set the owning side to null (unless already changed)
            if ($PositionOrdre->getPosition() === $this) {
                $PositionOrdre->setPosition(null);
            }
        }

        return $this;
    }

    public function getEstSolde(): ?bool
    {
        return $this->est_solde;
    }

    public function setEstSolde(?bool $est_solde): self
    {
        $this->est_solde = $est_solde;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->Compte;
    }

    public function setCompte(?Compte $Compte): self
    {
        $this->Compte = $Compte;

        return $this;
    }
}
