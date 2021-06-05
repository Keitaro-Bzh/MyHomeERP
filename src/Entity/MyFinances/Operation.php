<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Personne;
use App\Entity\MyContacts\Societe;
use App\Repository\MyFinances\OperationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OperationRepository::class)
 * @ORM\Table(name= "myfinances_operations")
 * @ORM\HasLifecycleCallbacks
 */
class Operation
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
    private $date;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $typeOperation;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $modePaiementTrigramme;

    /**
     * @ORM\ManyToOne(targetEntity=ModePaiement::class, inversedBy="operations")
     */
    private $modePaiement;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $debit;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $credit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $virementID;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numeroCheque;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $typeTiers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tiers_libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $est_pointe;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="operations")
     */
    private $Personne;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="operations")
     */
    private $Societe;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="operations")
     */
    private $Categorie;

    /**
     * @ORM\OneToOne(targetEntity=EcheanceOperation::class, cascade={"persist", "remove"})
     */
    private $echeance_operation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTypeOperation(): ?string
    {
        return $this->typeOperation;
    }

    public function setTypeOperation(string $typeOperation): self
    {
        $this->typeOperation = $typeOperation;

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

    public function getModePaiementTrigramme(): ?String
    {
        return $this->modePaiementTrigramme;
    }

    public function setModePaiementTrigramme(?string $modePaiementTrigramme): self
    {
        $this->modePaiementTrigramme = $modePaiementTrigramme;

        return $this;
    }

    public function getModePaiement(): ?ModePaiement
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?ModePaiement $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getDebit(): ?float
    {
        return $this->debit;
    }

    public function setDebit(?float $debit): self
    {
        $this->debit = $debit;

        return $this;
    }

    public function getCredit(): ?float
    {
        return $this->credit;
    }

    public function setCredit(?float $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function getVirementID(): ?int
    {
        return $this->virementID;
    }

    public function setVirementID(?int $virementID): self
    {
        $this->virementID = $virementID;

        return $this;
    }

    public function getNumeroCheque(): ?int
    {
        return $this->numeroCheque;
    }

    public function setNumeroCheque(?int $numeroCheque): self
    {
        $this->numeroCheque = $numeroCheque;

        return $this;
    }

    public function getEstPointe(): ?bool
    {
        return $this->est_pointe;
    }

    public function setEstPointe(?bool $est_pointe): self
    {
        $this->est_pointe = $est_pointe;

        return $this;
    }

    public function getTypeTiers(): ?string
    {
        return $this->typeTiers;
    }

    public function setTypeTiers(?string $typeTiers): self
    {
        $this->typeTiers = $typeTiers;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTiersLibelle(): ?string
    {
        return $this->tiers_libelle;
    }

    public function setTiersLibelle(?string $tiers_libelle): self
    {
        $this->tiers_libelle = $tiers_libelle;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->Personne;
    }

    public function setPersonne(?Personne $Personne): self
    {
        $this->Personne = $Personne;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->Societe;
    }

    public function setSociete(?Societe $Societe): self
    {
        $this->Societe = $Societe;

        return $this;
    }

    public function getCategorie(): ?SousCategorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?SousCategorie $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getEcheanceOperation(): ?EcheanceOperation
    {
        return $this->echeance_operation;
    }

    public function setEcheanceOperation(?EcheanceOperation $echeance_operation): self
    {
        $this->echeance_operation = $echeance_operation;

        return $this;
    }

}
