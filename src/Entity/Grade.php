<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=11)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personnel", mappedBy="grade")
     */
    private $personnels;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelleCourt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelleLong;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $actif;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
            $personnel->setGrade($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->contains($personnel)) {
            $this->personnels->removeElement($personnel);
            // set the owning side to null (unless already changed)
            if ($personnel->getGrade() === $this) {
                $personnel->setGrade(null);
            }
        }

        return $this;
    }

    public function getLibelleCourt(): ?string
    {
        return $this->libelleCourt;
    }

    public function setLibelleCourt(string $libelleCourt): self
    {
        $this->libelleCourt = $libelleCourt;

        return $this;
    }

    public function getLibelleLong(): ?string
    {
        return $this->libelleLong;
    }

    public function setLibelleLong(string $libelleLong): self
    {
        $this->libelleLong = $libelleLong;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getActif(): ?string
    {
        return $this->actif;
    }

    public function setActif($actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function __toString() {
        if($this->libelleLong != ""){
            return $this->libelleLong;
        }else{
            return "";
        }
        
    }
}
