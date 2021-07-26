<?php

namespace App\Entity\MyFinances;

use App\Repository\MyFinances\PositionOrdreRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=PositionOrdreRepository::class)
 * @ORM\Table(name= "myfinances_positionOrdres")
 * @ORM\HasLifecycleCallbacks
 */
class PositionOrdre
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
    private $date;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $typeMouvement;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_titres;

    /**
     * @ORM\Column(type="float")
     */
    private $valeur_titre;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taxe;

    /**
     * @ORM\Column(type="float")
     */
    private $frais;

    /**
     * @ORM\ManyToOne(targetEntity=Position::class, inversedBy="PositionOrdres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTypeMouvement(): ?string
    {
        return $this->typeMouvement;
    }

    public function setTypeMouvement(string $typeMouvement): self
    {
        $this->typeMouvement = $typeMouvement;

        return $this;
    }

    public function getNombreTitres(): ?int
    {
        return $this->nombre_titres;
    }

    public function setNombreTitres(int $nombre_titres): self
    {
        $this->nombre_titres = $nombre_titres;

        return $this;
    }

    public function getValeurTitre(): ?float
    {
        return $this->valeur_titre;
    }

    public function setValeurTitre(float $valeur_titre): self
    {
        $this->valeur_titre = $valeur_titre;

        return $this;
    }

    public function getTaxe(): ?float
    {
        return $this->taxe;
    }

    public function setTaxe(?float $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->Position;
    }

    public function setPosition(?Position $Position): self
    {
        $this->Position = $Position;

        return $this;
    }
}
