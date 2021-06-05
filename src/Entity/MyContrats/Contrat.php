<?php

namespace App\Entity\MyContrats;

use App\Entity\MyContacts\Societe;
use App\Entity\MyFinances\SousCategorie;
use App\Repository\MyContrats\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=ContratRepository::class)
 * @ORM\Table(name= "mycontrats_contrats")
 * @ORM\HasLifecycleCallbacks
 */
class Contrat
{
    use suiviLog;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="societes_contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Societe;

    /**
     * @ORM\Column(type="date")
     */
    private $date_signature;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $reference_contrat;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_fin_contrat;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="sousCategories_contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Categorie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $facturable;

    /**
     * @ORM\OneToMany(targetEntity=ContratFacturation::class, mappedBy="Contrat", orphanRemoval=true)
     */
    private $contrats_facturations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $est_archive;

    public function __construct()
    {
        $this->contrats_facturations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->date_signature;
    }

    public function setDateSignature(\DateTimeInterface $date_signature): self
    {
        $this->date_signature = $date_signature;

        return $this;
    }

    public function getReferenceContrat(): ?string
    {
        return $this->reference_contrat;
    }

    public function setReferenceContrat(?string $reference_contrat): self
    {
        $this->reference_contrat = $reference_contrat;

        return $this;
    }

    public function getDateFinContrat(): ?\DateTimeInterface
    {
        return $this->date_fin_contrat;
    }

    public function setDateFinContrat(?\DateTimeInterface $date_fin_contrat): self
    {
        $this->date_fin_contrat = $date_fin_contrat;

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

    public function getFacturable(): ?bool
    {
        return $this->facturable;
    }

    public function setFacturable(?bool $facturable): self
    {
        $this->facturable = $facturable;

        return $this;
    }

    /**
     * @return Collection|ContratFacturation[]
     */
    public function getContratsFacturations(): Collection
    {
        return $this->contrats_facturations;
    }

    public function addContratsFacturation(ContratFacturation $contratsFacturation): self
    {
        if (!$this->contrats_facturations->contains($contratsFacturation)) {
            $this->contrats_facturations[] = $contratsFacturation;
            $contratsFacturation->setContrat($this);
        }

        return $this;
    }

    public function removeContratsFacturation(ContratFacturation $contratsFacturation): self
    {
        if ($this->contrats_facturations->removeElement($contratsFacturation)) {
            // set the owning side to null (unless already changed)
            if ($contratsFacturation->getContrat() === $this) {
                $contratsFacturation->setContrat(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
}
