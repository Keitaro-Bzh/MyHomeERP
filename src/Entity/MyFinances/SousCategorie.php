<?php

namespace App\Entity\MyFinances;

use App\Entity\MyContrats\Contrat;
use App\Repository\MyFinances\SousCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=SousCategorieRepository::class)
 * @ORM\Table(name= "myfinances_sousCategories")
 * @ORM\HasLifecycleCallbacks
 */
class SousCategorie
{
    use suiviLog;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="sousCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Categorie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="Categorie")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="Categorie")
     */
    private $echeances;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="sous_categorie")
     */
    private $sous_categorie_echeances;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="sous_categorie")
     */
    private $credits_sous_categories;

    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="Categorie", orphanRemoval=true)
     */
    private $sousCategories_contrats;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
        $this->echeances = new ArrayCollection();
        $this->sous_categorie_echeances = new ArrayCollection();
        $this->credits_sous_categories = new ArrayCollection();
        $this->sousCategories_contrats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
            $operation->setCategorie($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getCategorie() === $this) {
                $operation->setCategorie(null);
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

    /**
     * @return Collection|Echeance[]
     */
    public function getSousCategorieEcheances(): Collection
    {
        return $this->sous_categorie_echeances;
    }

    public function addSousCategorieEcheance(Echeance $sousCategorieEcheance): self
    {
        if (!$this->sous_categorie_echeances->contains($sousCategorieEcheance)) {
            $this->sous_categorie_echeances[] = $sousCategorieEcheance;
            $sousCategorieEcheance->setSousCategorie($this);
        }

        return $this;
    }

    public function removeSousCategorieEcheance(Echeance $sousCategorieEcheance): self
    {
        if ($this->sous_categorie_echeances->removeElement($sousCategorieEcheance)) {
            // set the owning side to null (unless already changed)
            if ($sousCategorieEcheance->getSousCategorie() === $this) {
                $sousCategorieEcheance->setSousCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Credit[]
     */
    public function getCreditsSousCategories(): Collection
    {
        return $this->credits_sous_categories;
    }

    public function addCreditsSousCategory(Credit $creditsSousCategory): self
    {
        if (!$this->credits_sous_categories->contains($creditsSousCategory)) {
            $this->credits_sous_categories[] = $creditsSousCategory;
            $creditsSousCategory->setSousCategorie($this);
        }

        return $this;
    }

    public function removeCreditsSousCategory(Credit $creditsSousCategory): self
    {
        if ($this->credits_sous_categories->removeElement($creditsSousCategory)) {
            // set the owning side to null (unless already changed)
            if ($creditsSousCategory->getSousCategorie() === $this) {
                $creditsSousCategory->setSousCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrat[]
     */
    public function getSousCategoriesContrats(): Collection
    {
        return $this->sousCategories_contrats;
    }

    public function addSousCategoriesContrat(Contrat $sousCategoriesContrat): self
    {
        if (!$this->sousCategories_contrats->contains($sousCategoriesContrat)) {
            $this->sousCategories_contrats[] = $sousCategoriesContrat;
            $sousCategoriesContrat->setCategorie($this);
        }

        return $this;
    }

    public function removeSousCategoriesContrat(Contrat $sousCategoriesContrat): self
    {
        if ($this->sousCategories_contrats->removeElement($sousCategoriesContrat)) {
            // set the owning side to null (unless already changed)
            if ($sousCategoriesContrat->getCategorie() === $this) {
                $sousCategoriesContrat->setCategorie(null);
            }
        }

        return $this;
    }

}
