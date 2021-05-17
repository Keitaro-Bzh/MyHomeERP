<?php

namespace App\Entity\MyFinances;

use App\Repository\MyFinances\TypeCompteRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\suiviLog;

/**
 * @ORM\Entity(repositoryClass=TypeCompteRepository::class)
 * @ORM\Table(name= "myfinances_typesCompte")
 * @ORM\HasLifecycleCallbacks
 */
class TypeCompte
{
    use suiviLog;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $retraitOK;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $depotChequeOK;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $carteOK;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $autreModePaiementOK;

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

    public function getRetraitOK(): ?bool
    {
        return $this->retraitOK;
    }

    public function setRetraitOK(?bool $retraitOK): self
    {
        $this->retraitOK = $retraitOK;

        return $this;
    }

    public function getDepotChequeOK(): ?bool
    {
        return $this->depotChequeOK;
    }

    public function setDepotChequeOK(?bool $depotChequeOK): self
    {
        $this->depotChequeOK = $depotChequeOK;

        return $this;
    }

    public function getCarteOK(): ?bool
    {
        return $this->carteOK;
    }

    public function setCarteOK(?bool $carteOK): self
    {
        $this->carteOK = $carteOK;

        return $this;
    }

    public function getAutreModePaiementOK(): ?bool
    {
        return $this->autreModePaiementOK;
    }

    public function setAutreModePaiementOK(?bool $autreModePaiementOK): self
    {
        $this->autreModePaiementOK = $autreModePaiementOK;

        return $this;
    }
}
