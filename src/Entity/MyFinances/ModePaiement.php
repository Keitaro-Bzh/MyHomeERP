<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Personne;
use App\Repository\MyFinances\ModePaiementRepository;
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
    private $typePaiement;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="modePaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulaire;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class)
     */
    private $coTitulaire;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePaiement(): ?string
    {
        return $this->typePaiement;
    }

    public function setTypePaiement(string $typePaiement): self
    {
        $this->typePaiement = $typePaiement;

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

    public function getCoTitulaire(): ?Personne
    {
        return $this->coTitulaire;
    }

    public function setCoTitulaire(?Personne $coTitulaire): self
    {
        $this->coTitulaire = $coTitulaire;

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
}
