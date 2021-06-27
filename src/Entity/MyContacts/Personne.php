<?php

namespace App\Entity\MyContacts;

use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\ModePaiement;
use App\Entity\MyFinances\Operation;
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
 * @Vich\Uploadable
 */
class Personne
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

    private $archive;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="titulaire", orphanRemoval=true)
     */
    private $titulaire;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="cotitulaire", orphanRemoval=true)
     */
    private $coTitulaire;

    /**
     * @ORM\OneToMany(targetEntity=ModePaiement::class, mappedBy="titulaire", orphanRemoval=true)
     */
    private $modePaiements;

    /**
     * @Vich\UploadableField(mapping="personnes_photos", fileNameProperty="photo")
     * @Assert\Image(maxSize="1M")
     * @var File|null
     */
    private $imagePhoto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="Personne")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="tiers_personne")
     */
    private $echeances;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $libelle_adresse;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $ville;

    public function __construct()
    {
        $this->titulaire = new ArrayCollection();
        $this->coTitulaire = new ArrayCollection();
        $this->modePaiements = new ArrayCollection();
        $this->operations = new ArrayCollection();
        $this->echeances = new ArrayCollection();
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

        /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imagePhoto
     */
    public function setImagePhoto(?File $imagePhoto = null)
    {
        $this->imagePhoto = $imagePhoto;

        if (null !== $imagePhoto) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setDateModification(new \DateTimeImmutable());
        }
    }

    public function getImagePhoto(): ?File
    {
        return $this->imagePhoto;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(?bool $archive): self
    {
        $this->archive = $archive;

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
            $operation->setPersonne($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getPersonne() === $this) {
                $operation->setPersonne(null);
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
            $echeance->setTiersPersonne($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            // set the owning side to null (unless already changed)
            if ($echeance->getTiersPersonne() === $this) {
                $echeance->setTiersPersonne(null);
            }
        }

        return $this;
    }

    public function getLibelleAdresse(): ?string
    {
        return $this->libelle_adresse;
    }

    public function setLibelleAdresse(?string $libelle_adresse): self
    {
        $this->libelle_adresse = $libelle_adresse;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(?int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

}
