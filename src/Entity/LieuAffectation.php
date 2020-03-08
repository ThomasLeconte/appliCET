<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuAffectationRepository")
 */
class LieuAffectation
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     */
    private $rne;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personnel", mappedBy="affectation")
     */
    private $personnels;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $secteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sigle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeEtablissement;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getRne(): ?string
    {
        return $this->rne;
    }

    public function setRne(string $rne): self
    {
        $this->rne = $rne;

        return $this;
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

    /**
     * @return Collection|Personnel[]
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels[] = $personnel;
            $personnel->setAffectation($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->contains($personnel)) {
            $this->personnels->removeElement($personnel);
            // set the owning side to null (unless already changed)
            if ($personnel->getAffectation() === $this) {
                $personnel->setAffectation(null);
            }
        }

        return $this;
    }

    public function __toString() {
        if($this->libelle != ""){
            return $this->libelle;
        }else{
            return "";
        }
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(string $secteur): self
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }

    public function getTypeEtablissement(): ?string
    {
        return $this->typeEtablissement;
    }

    public function setTypeEtablissement(string $typeEtablissement): self
    {
        $this->typeEtablissement = $typeEtablissement;

        return $this;
    }
}
