<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Personne;
use App\Entity\MyContacts\Societe;
use App\Entity\MyContrats\ContratFacturation;
use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\Credit;
use App\Repository\MyFinances\EcheanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;
use App\Entity\Traits\MyFinances\EcheanceTrait;

/**
 * @ORM\Entity(repositoryClass=EcheanceRepository::class)
 * @ORM\Table(name= "myfinances_echeances")
 * @ORM\HasLifecycleCallbacks
 */
class Echeance
{
    use suiviLog;  
    use EcheanceTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="echeances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    /**
     * @ORM\ManyToOne(targetEntity=Credit::class, inversedBy="echeances")
     */
    private $Credit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $est_solde;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $type_tiers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tiers_libelle;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="echeances")
     */
    private $tiers_personne;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="echeances")
     */
    private $tiers_societe;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_echeances;

    /**
     * @ORM\Column(type="date")
     */
    private $date_echeance_one;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_total;

    /**
     * @ORM\OneToMany(targetEntity=EcheanceOperation::class, mappedBy="echeance", orphanRemoval=true)
     */
    private $echeance_operation;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $mode_paiement_trigramme;

    /**
     * @ORM\ManyToOne(targetEntity=ModePaiement::class, inversedBy="echeances")
     */
    private $mode_paiement;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $type_operation;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="echeances_virement")
     */
    private $compte_destinataire_virement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recalcul_operation_anterieur;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="sous_categorie_echeances")
     */
    private $sous_categorie;

    /**
     * @ORM\OneToOne(targetEntity=ContratFacturation::class, mappedBy="Echeance", cascade={"persist", "remove"})
     */
    private $echeances_contratFacturation;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $frequence_paiement;

    /**
     * @ORM\Column(type="integer")
     */
    private $frequence_nombre_paiement;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_fin;

    public function __construct()
    {
        $this->echeance_operation = new ArrayCollection();
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

    public function getCredit(): ?Credit
    {
        return $this->Credit;
    }

    public function setCredit(?Credit $Credit): self
    {
        $this->Credit = $Credit;

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

    public function getTypeTiers(): ?string
    {
        return $this->type_tiers;
    }

    public function setTypeTiers(string $type_tiers): self
    {
        $this->type_tiers = $type_tiers;

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

    public function getTiersPersonne(): ?Personne
    {
        return $this->tiers_personne;
    }

    public function setTiersPersonne(?Personne $tiers_personne): self
    {
        $this->tiers_personne = $tiers_personne;

        return $this;
    }

    public function getRecalculOperationAnterieur(): ?bool
    {
        return $this->recalcul_operation_anterieur;
    }

    public function setRecalculOperationAnterieur(?bool $recalcul_operation_anterieur): self
    {
        $this->recalcul_operation_anterieur = $recalcul_operation_anterieur;

        return $this;
    }

    public function getTiersSociete(): ?Societe
    {
        return $this->tiers_societe;
    }

    public function setTiersSociete(?Societe $tiers_societe): self
    {
        $this->tiers_societe = $tiers_societe;

        return $this;
    }

    public function getNombreEcheances(): ?int
    {
        return $this->nombre_echeances;
    }

    public function setNombreEcheances(int $nombre_echeances): self
    {
        $this->nombre_echeances = $nombre_echeances;

        return $this;
    }

    public function getDateEcheanceOne(): ?\DateTimeInterface
    {
        return $this->date_echeance_one;
    }

    public function setDateEcheanceOne(\DateTimeInterface $date_echeance_one): self
    {
        $this->date_echeance_one = $date_echeance_one;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): self
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    /**
     * @return Collection|EcheanceOperation[]
     */
    public function getEcheanceOperation(): Collection
    {
        return $this->echeance_operation;
    }

    public function addEcheanceOperation(EcheanceOperation $echeanceOperation): self
    {
        if (!$this->echeance_operation->contains($echeanceOperation)) {
            $this->echeance_operation[] = $echeanceOperation;
            $echeanceOperation->setEcheance($this);
        }

        return $this;
    }

    public function removeEcheanceOperation(EcheanceOperation $echeanceOperation): self
    {
        if ($this->echeance_operation->removeElement($echeanceOperation)) {
            // set the owning side to null (unless already changed)
            if ($echeanceOperation->getEcheance() === $this) {
                $echeanceOperation->setEcheance(null);
            }
        }

        return $this;
    }

    public function getModePaiementTrigramme(): ?string
    {
        return $this->mode_paiement_trigramme;
    }

    public function setModePaiementTrigramme(string $mode_paiement_trigramme): self
    {
        $this->mode_paiement_trigramme = $mode_paiement_trigramme;

        return $this;
    }

    public function getModePaiement(): ?ModePaiement
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?ModePaiement $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getTypeOperation(): ?string
    {
        return $this->type_operation;
    }

    public function setTypeOperation(?string $type_operation): self
    {
        $this->type_operation = $type_operation;

        return $this;
    }

    public function getCompteDestinataireVirement(): ?Compte
    {
        return $this->compte_destinataire_virement;
    }

    public function setCompteDestinataireVirement(?Compte $compte_destinataire_virement): self
    {
        $this->compte_destinataire_virement = $compte_destinataire_virement;

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

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sous_categorie;
    }

    public function setSousCategorie(?SousCategorie $sous_categorie): self
    {
        $this->sous_categorie = $sous_categorie;

        return $this;
    }

    public function getEcheancesContratFacturation(): ?ContratFacturation
    {
        return $this->echeances_contratFacturation;
    }

    public function setEcheancesContratFacturation(ContratFacturation $echeances_contratFacturation): self
    {
        // set the owning side of the relation if necessary
        if ($echeances_contratFacturation->getEcheance() !== $this) {
            $echeances_contratFacturation->setEcheance($this);
        }

        $this->echeances_contratFacturation = $echeances_contratFacturation;

        return $this;
    }

    public function getFrequencePaiement(): ?string
    {
        return $this->frequence_paiement;
    }

    public function setFrequencePaiement(string $frequence_paiement): self
    {
        $this->frequence_paiement = $frequence_paiement;

        return $this;
    }

    public function getFrequenceNombrePaiement(): ?int
    {
        return $this->frequence_nombre_paiement;
    }

    public function setFrequenceNombrePaiement(int $frequence_nombre_paiement): self
    {
        $this->frequence_nombre_paiement = $frequence_nombre_paiement;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

}
