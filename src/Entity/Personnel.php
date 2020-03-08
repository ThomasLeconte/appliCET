<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonnelRepository")
 */
class Personnel
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=25)
     */
    private $numen;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idLdap;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etatCloture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeCloture", inversedBy="personnels")
     */
    private $typeCloture;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LieuAffectation", inversedBy="users")
     * @ORM\JoinColumn(name="lieu_affectation", referencedColumnName="rne")
     */
    private $affectation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Action", mappedBy="personnel", orphanRemoval=true)
     */
    private $actions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Grade", inversedBy="personnels")
     * @ORM\JoinColumn(name="grade", referencedColumnName="code")
     */
    private $grade;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomPatronymique;

    /**
     * Correspond à la source ou a été instancié le personnel
     */
    private $source; 

    public function __construct()
    {
        $this->action = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getNumen(): ?string
    {
        return $this->numen;
    }

    public function setNumen(string $numen): self
    {
        $this->numen = $numen;

        return $this;
    }

    public function getIdLdap(): ?string
    {
        return $this->idLdap;
    }

    public function setIdLdap(string $idLdap): self
    {
        $this->idLdap = $idLdap;

        return $this;
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

    public function getEtatCloture(): ?bool
    {
        return $this->etatCloture;
    }

    public function setEtatCloture(bool $etatCloture): self
    {
        $this->etatCloture = $etatCloture;

        return $this;
    }

    public function getTypeCloture(): ?TypeCloture
    {
        return $this->typeCloture;
    }

    public function setTypeCloture(?TypeCloture $typeCloture): self
    {
        $this->typeCloture = $typeCloture;

        return $this;
    }

    public function getAffectation(): ?LieuAffectation
    {
        return $this->affectation;
    }

    public function setAffectation(?LieuAffectation $affectation): self
    {
        $this->affectation = $affectation;

        return $this;
    }
    
    public function __toString() {
        return $this->prenom." ".$this->nom;
    }

    /**
     * @return Collection|Action[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setUser($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getUser() === $this) {
                $action->setUser(null);
            }
        }

        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getNomPatronymique(): ?string
    {
        return $this->nomPatronymique;
    }

    public function setNomPatronymique(?string $nomPatronymique): self
    {
        $this->nomPatronymique = $nomPatronymique;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

}
