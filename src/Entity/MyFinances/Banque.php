<?php

namespace App\Entity\MyFinances;

use App\Entity\MyFinances\Compte;
use App\Entity\MyContacts\Societe;
use App\Repository\MyFinances\BanqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=BanqueRepository::class)
 * @ORM\Table(name= "myfinances_banques")
 * @ORM\HasLifecycleCallbacks
 */
class Banque
{
    use suiviLog;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Societe::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $codeBanque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guichet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $codeGuichet;

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="banque", orphanRemoval=true)
     */
    private $comptes;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getCodeBanque(): ?int
    {
        return $this->codeBanque;
    }

    public function setCodeBanque(?int $codeBanque): self
    {
        $this->codeBanque = $codeBanque;

        return $this;
    }

    public function getGuichet(): ?string
    {
        return $this->guichet;
    }

    public function setGuichet(?string $guichet): self
    {
        $this->guichet = $guichet;

        return $this;
    }

    public function getCodeGuichet(): ?int
    {
        return $this->codeGuichet;
    }

    public function setCodeGuichet(?int $codeGuichet): self
    {
        $this->codeGuichet = $codeGuichet;

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setBanque($this);
        }

        return $this;
    }

    public function removeBanque(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getBanque() === $this) {
                $compte->setBanque(null);
            }
        }

        return $this;
    }
}
