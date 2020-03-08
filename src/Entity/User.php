<?php

namespace App\Entity;

use App\Tool\Interfaces\ICet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }
    
    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /*========================================*/
    /*===========SECURITY METHODS=============*/
    /*========================================*/
    public function eraseCredentials(){

    }

    public function getSalt(){

    }

    public function getRoles(){
        switch($this->role->getLibelle()){
            case(ICet::CET_DSI):
                return ICet::CET_ROLE_DSI;
            break;
            case(ICet::CET_GESTIONNAIRE):
                return ICet::CET_ROLE_GESTIONNAIRE;
            break;
        }
    }
}
