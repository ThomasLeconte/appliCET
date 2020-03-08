<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActionRepository")
 */
class Action
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $annee;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $jours;

    /**
     * @ORM\Column(type="integer")
     */
    private $conges;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeAction", inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeAction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personnel", inversedBy="actions")
     * @ORM\JoinColumn(name="personnel", referencedColumnName="numen")
     */
    private $personnel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
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

    public function getJours(): ?int
    {
        return $this->jours;
    }

    public function setJours(int $jours): self
    {
        $this->jours = $jours;

        return $this;
    }

    public function getConges(): ?int
    {
        return $this->conges;
    }

    public function setConges(int $conges): self
    {
        $this->conges = $conges;

        return $this;
    }

    public function getTypeAction(): ?TypeAction
    {
        return $this->typeAction;
    }

    public function setTypeAction(?TypeAction $typeAction): self
    {
        $this->typeAction = $typeAction;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }
}
