<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContacts\Personne;
use App\Entity\MyFinances\Banque;
use App\Repository\MyFinances\CompteRepository;
use App\Entity\MyContacts\Societe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

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
     */
    private $libelle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="float")
     */
    private $soldeInitial;

    /**
     * @ORM\ManyToOne(targetEntity=Banque::class, inversedBy="Banque")
     * @ORM\JoinColumn(nullable=false)
     */
    private $banque;

    /**
     * @ORM\Column(type="string", length=5)
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

    public function __construct()
    {
        $this->modePaiements = new ArrayCollection();
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
}
