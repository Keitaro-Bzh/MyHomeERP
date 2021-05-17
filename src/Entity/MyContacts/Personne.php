<?php

namespace App\Entity\MyContacts;

use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\ModePaiement;
use App\Repository\MyContacts\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=PersonneRepository::class)
 * @ORM\Table(name= "mycontacts_personnes")
 * @ORM\HasLifecycleCallbacks
 */
class Personne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */

    private $estActif;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="titulaire")
     */
    private $titulaire;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="cotitulaire")
     */
    private $coTitulaire;

    /**
     * @ORM\OneToMany(targetEntity=ModePaiement::class, mappedBy="titulaire", orphanRemoval=true)
     */
    private $modePaiements;

    public function __construct()
    {
        $this->titulaire = new ArrayCollection();
        $this->coTitulaire = new ArrayCollection();
        $this->modePaiements = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(?bool $estActif): self
    {
        $this->estActif = $estActif;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getCoTitulaire(): Collection
    {
        return $this->coTitulaire;
    }

    public function addCoTitulaire(Compte $coTitulaire): self
    {
        if (!$this->coTitulaire->contains($coTitulaire)) {
            $this->coTitulaire[] = $coTitulaire;
            $coTitulaire->setTitulaire($this);
        }

        return $this;
    }

    public function removeCoTitulaire(Compte $coTitulaire): self
    {
        if ($this->coTitulaire->removeElement($coTitulaire)) {
            // set the owning side to null (unless already changed)
            if ($coTitulaire->getTitulaire() === $this) {
                $coTitulaire->setTitulaire(null);
            }
        }

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
            $modePaiement->setTitulaire($this);
        }

        return $this;
    }

    public function removeModePaiement(ModePaiement $modePaiement): self
    {
        if ($this->modePaiements->removeElement($modePaiement)) {
            // set the owning side to null (unless already changed)
            if ($modePaiement->getTitulaire() === $this) {
                $modePaiement->setTitulaire(null);
            }
        }

        return $this;
    }
}
