<?php

namespace App\Entity\Traits;

trait suiviLog
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idModification;

    public function getdateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setdateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getIdCreation(): ?int
    {
        dd($this->getUser());
        return $this->id;
    }

    
    public function setIdCreationn(?int $idCreation): self
    {
        $this->id = $idCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getIdModification(): ?int
    {
        return $this->id;
    }

    
    public function setIdModification(?int $idModification): self
    {
        $this->id = $idModification;

        return $this;
    }

    /** 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function majLog() {
        if ($this->getdateCreation() == null) {
            $this->setdateCreation(new \DateTimeImmutable());
            //$this->setIdCreationn($this->getUser()->getId());
        }

        $this->setDateModification(new \DateTimeImmutable());
        //$this->setIdModification($this->getUser()->getId());
    }

}