<?php

namespace App\Entity\MyContacts;

use App\Entity\MyContrats\Contrat;
use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\Operation;
use App\Repository\MyContacts\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=SocieteRepository::class)
 * @ORM\Table(name= "mycontacts_societes")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Societe
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
     * @Vich\UploadableField(mapping="societes_logos", fileNameProperty="logo")
     * @Assert\Image(maxSize="1M")
     * @var File|null
     */
    private $imageLogo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $estBanque;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="Societe")
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Echeance::class, mappedBy="tiers_societe")
     */
    private $echeances;

    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="Societe", orphanRemoval=true)
     */
    private $societes_contrats;

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
        $this->operations = new ArrayCollection();
        $this->echeances = new ArrayCollection();
        $this->societes_contrats = new ArrayCollection();
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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageLogo
     */
    public function setImageLogo(?File $imageLogo = null)
    {
        $this->imageLogo = $imageLogo;

        if (null !== $imageLogo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setDateModification(new \DateTimeImmutable());
        }
    }

    public function getImageLogo(): ?File
    {
        return $this->imageLogo;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getEstBanque(): ?bool
    {
        return $this->estBanque;
    }

    public function setEstBanque(?bool $estBanque): self
    {
        $this->estBanque = $estBanque;

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
            $operation->setSociete($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getSociete() === $this) {
                $operation->setSociete(null);
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
            $echeance->setTiersSociete($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            // set the owning side to null (unless already changed)
            if ($echeance->getTiersSociete() === $this) {
                $echeance->setTiersSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contrat[]
     */
    public function getSocietesContrats(): Collection
    {
        return $this->societes_contrats;
    }

    public function addSocietesContrat(Contrat $societesContrat): self
    {
        if (!$this->societes_contrats->contains($societesContrat)) {
            $this->societes_contrats[] = $societesContrat;
            $societesContrat->setSociete($this);
        }

        return $this;
    }

    public function removeSocietesContrat(Contrat $societesContrat): self
    {
        if ($this->societes_contrats->removeElement($societesContrat)) {
            // set the owning side to null (unless already changed)
            if ($societesContrat->getSociete() === $this) {
                $societesContrat->setSociete(null);
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
