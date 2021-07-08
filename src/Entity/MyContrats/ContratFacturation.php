<?php

namespace App\Entity\MyContrats;

use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\Echeance;
use App\Repository\MyContrats\ContratFacturationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=ContratFacturationRepository::class)
 * @ORM\Table(name= "mycontrats_contratsFacturation")
 * @ORM\HasLifecycleCallbacks
 */
class ContratFacturation
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
    private $date_debut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_fin;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $frequence_paiement;

        /**
     * @ORM\Column(type="integer")
     */
    private $frequence_nombre_paiement;

    /**
     * @ORM\ManyToOne(targetEntity=Contrat::class, inversedBy="contrats_facturations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Contrat;

    /**
     * @ORM\OneToOne(targetEntity=Echeance::class, inversedBy="echeances_contratFacturation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Echeance;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="contratFacturations")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Positive(message="Le libellé du compte est obligatoire")
     */
    private $Compte;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recalcul_operation_anterieur;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $type_mouvement;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $est_archive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $montant_fraction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

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

    public function getFrequencePaiement(): ?string
    {
        return $this->frequence_paiement;
    }

    public function setFrequencePaiement(string $frequence_paiement): self
    {
        $this->frequence_paiement = $frequence_paiement;

        return $this;
    }

    public function getContrat(): ?Contrat
    {
        return $this->Contrat;
    }

    public function setContrat(?Contrat $Contrat): self
    {
        $this->Contrat = $Contrat;

        return $this;
    }

    public function getEcheance(): ?Echeance
    {
        return $this->Echeance;
    }

    public function setEcheance(Echeance $Echeance): self
    {
        $this->Echeance = $Echeance;

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

    public function getFrequenceNombrePaiement(): ?int
    {
        return $this->frequence_nombre_paiement;
    }

    public function setFrequenceNombrePaiement(int $frequence_nombre_paiement): self
    {
        $this->frequence_nombre_paiement = $frequence_nombre_paiement;

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

    public function getTypeMouvement(): ?string
    {
        return $this->type_mouvement;
    }

    public function setTypeMouvement(string $type_mouvement): self
    {
        $this->type_mouvement = $type_mouvement;

        return $this;
    }

    public function getEstArchive(): ?bool
    {
        return $this->est_archive;
    }

    public function setEstArchive(?bool $est_archive): self
    {
        $this->est_archive = $est_archive;

        return $this;
    }

    public function getMontantFraction(): ?bool
    {
        return $this->montant_fraction;
    }

    public function setMontantFraction(?bool $montant_fraction): self
    {
        $this->montant_fraction = $montant_fraction;

        return $this;
    }
}
