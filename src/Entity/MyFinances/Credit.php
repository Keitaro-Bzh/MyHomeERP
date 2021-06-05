<?php

namespace App\Entity\MyFinances;

use App\Entity\MyFinances\Echeance;
use App\Repository\MyFinances\CreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=CreditRepository::class)
 * @ORM\Table(name= "myfinances_credits")
 * @ORM\HasLifecycleCallbacks
 */
class Credit
{
    use suiviLog;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="credits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateSignature;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEcheanceOne;

    /**
     * @ORM\Column(type="float")
     */
    private $taux;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montantAssurance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $estCloture;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="Credit", cascade={"persist", "remove"})
     */
    private $echeances;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="credits_sous_categories")
     */
    private $sous_categorie;

    public function __construct()
    {
        $this->echeances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->dateSignature;
    }

    public function setDateSignature(?\DateTimeInterface $dateSignature): self
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    public function getDateEcheanceOne(): ?\DateTimeInterface
    {
        return $this->dateEcheanceOne;
    }

    public function setDateEcheanceOne(\DateTimeInterface $dateEcheanceOne): self
    {
        $this->dateEcheanceOne = $dateEcheanceOne;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(float $taux): self
    {
        $this->taux = $taux;

        return $this;
    }

    public function getMontantAssurance(): ?float
    {
        return $this->montantAssurance;
    }

    public function setMontantAssurance(?float $montantAssurance): self
    {
        $this->montantAssurance = $montantAssurance;

        return $this;
    }

    public function getEstCloture(): ?bool
    {
        return $this->estCloture;
    }

    public function setEstCloture(?bool $estCloture): self
    {
        $this->estCloture = $estCloture;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $echeance->setCredit($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            // set the owning side to null (unless already changed)
            if ($echeance->getCredit() === $this) {
                $echeance->setCredit(null);
            }
        }

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sous_categorie;
    }

    public function setSousCategorie(?SousCategorie $sous_categorie): self
    {
        $this->sous_categorie = $sous_categorie;

        return $this;
    }
}
