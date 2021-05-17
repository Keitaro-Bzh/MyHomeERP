<?php

namespace App\Entity\MyContacts;

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
    private $estActif;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $estBanque;

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

    public function getEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(?bool $estActif): self
    {
        $this->estActif = $estActif;

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

}
