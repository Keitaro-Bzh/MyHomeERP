<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContrats\ContratFacturation;
use App\Entity\MyFinances\Echeance;
use App\Entity\MyContacts\Personne;
use App\Entity\MyFinances\Banque;
use App\Repository\MyFinances\CompteRepository;
use App\Entity\MyContacts\Societe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ORM\Table(name= "myfinances_comptes")
 * @ORM\HasLifecycleCallbacks
 */
class Compte
{
    use suiviLog;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Le libellé du compte est obligatoire")
     */
    private $libelle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotNull(message="Vous devez saisir un montant même nul")
     */
    private $soldeInitial;

    /**
     * @ORM\ManyToOne(targetEntity=Banque::class, inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $banque;

    /**
     * @ORM\Column(type="string", length=5)
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeCompte;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="titulaire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $titulaire;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="coTitulaire")
     */
    private $cotitulaire;

    /**
     * @ORM\OneToMany(targetEntity=ModePaiement::class, mappedBy="compte", orphanRemoval=true)
     */
    private $modePaiements;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="Compte", orphanRemoval=true)
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="Compte", orphanRemoval=true)
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="Compte")
     */
    private $echeances;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="compte_destinataire_virement")
     */
    private $echeances_virement;

    /**
     * @ORM\OneToMany(targetEntity=ContratFacturation::class, mappedBy="Compte", orphanRemoval=true)
     */
    private $contratFacturations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive;

    private $soldeCours;
    private $soldeReel;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="compte_virement_interne")
     */
    private $compte_virement_interne;

    public function __construct()
    {
        $this->modePaiements = new ArrayCollection();
        $this->operations = new ArrayCollection();
        $this->credits = new ArrayCollection();
        $this->echeances = new ArrayCollection();
        $this->echeances_virement = new ArrayCollection();
        $this->contratFacturations = new ArrayCollection();
        $this->compte_virement_interne = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getSoldeInitial(): ?float
    {
        return $this->soldeInitial;
    }

    public function setSoldeInitial(float $soldeInitial): self
    {
        $this->soldeInitial = $soldeInitial;

        return $this;
    }

    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): self
    {
        $this->banque = $banque;

        return $this;
    }

    public function getTypeCompte(): ?string
    {
        return $this->typeCompte;
    }

    public function setTypeCompte(string $typeCompte): self
    {
        $this->typeCompte = $typeCompte;

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

    public function getCotitulaire(): ?Personne
    {
        return $this->cotitulaire;
    }

    public function setCotitulaire(?Personne $cotitulaire): self
    {
        $this->cotitulaire = $cotitulaire;

        return $this;
    }

    /**
     * @return Collection|ModePaiement[]
     */
    public function getModePaiements(): Collection
    {
        return $this->modePaiements;
    }

    public function addModePaiement(ModePaiement $modePaiement): self
    {
        if (!$this->modePaiements->contains($modePaiement)) {
            $this->modePaiements[] = $modePaiement;
            $modePaiement->setCompte($this);
        }

        return $this;
    }

    public function removeModePaiement(ModePaiement $modePaiement): self
    {
        if ($this->modePaiements->removeElement($modePaiement)) {
            // set the owning side to null (unless already changed)
            if ($modePaiement->getCompte() === $this) {
                $modePaiement->setCompte(null);
            }
        }

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
            $operation->setCompte($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getCompte() === $this) {
                $operation->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Credit[]
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): self
    {
        if (!$this->credits->contains($credit)) {
            $this->credits[] = $credit;
            $credit->setCompte($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getCompte() === $this) {
                $credit->setCompte(null);
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
            $echeance->setCompte($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            // set the owning side to null (unless already changed)
            if ($echeance->getCompte() === $this) {
                $echeance->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Echeance[]
     */
    public function getEcheancesVirement(): Collection
    {
        return $this->echeances_virement;
    }

    public function addEcheancesVirement(Echeance $echeancesVirement): self
    {
        if (!$this->echeances_virement->contains($echeancesVirement)) {
            $this->echeances_virement[] = $echeancesVirement;
            $echeancesVirement->setCompteDestinataireVirement($this);
        }

        return $this;
    }

    public function removeEcheancesVirement(Echeance $echeancesVirement): self
    {
        if ($this->echeances_virement->removeElement($echeancesVirement)) {
            // set the owning side to null (unless already changed)
            if ($echeancesVirement->getCompteDestinataireVirement() === $this) {
                $echeancesVirement->setCompteDestinataireVirement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContratFacturation[]
     */
    public function getContratFacturations(): Collection
    {
        return $this->contratFacturations;
    }

    public function addContratFacturation(ContratFacturation $contratFacturation): self
    {
        if (!$this->contratFacturations->contains($contratFacturation)) {
            $this->contratFacturations[] = $contratFacturation;
            $contratFacturation->setCompte($this);
        }

        return $this;
    }

    public function removeContratFacturation(ContratFacturation $contratFacturation): self
    {
        if ($this->contratFacturations->removeElement($contratFacturation)) {
            // set the owning side to null (unless already changed)
            if ($contratFacturation->getCompte() === $this) {
                $contratFacturation->setCompte(null);
            }
        }

        return $this;
    }

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(?bool $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    public function getSoldeCours(): ?float
    {
        return $this->soldeCours;
    }

    public function setSoldeCours(float $soldeCours): self
    {
        $this->soldeCours = $soldeCours;

        return $this;
    }

    public function getSoldeReel(): ?float
    {
        return $this->soldeReel;
    }

    public function setSoldeReel(float $soldeReel): self
    {
        $this->soldeReel = $soldeReel;

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getCompteVirementInterne(): Collection
    {
        return $this->compte_virement_interne;
    }

    public function addCompteVirementInterne(Operation $compteVirementInterne): self
    {
        if (!$this->compte_virement_interne->contains($compteVirementInterne)) {
            $this->compte_virement_interne[] = $compteVirementInterne;
            $compteVirementInterne->setCompteVirementInterne($this);
        }

        return $this;
    }

    public function removeCompteVirementInterne(Operation $compteVirementInterne): self
    {
        if ($this->compte_virement_interne->removeElement($compteVirementInterne)) {
            // set the owning side to null (unless already changed)
            if ($compteVirementInterne->getCompteVirementInterne() === $this) {
                $compteVirementInterne->setCompteVirementInterne(null);
            }
        }

        return $this;
    }
}
