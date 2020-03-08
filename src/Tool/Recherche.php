<?php

namespace App\Tool;

use Symfony\Component\Validator\Constraints as Assert;

class Recherche{

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le nom que vous recherchez doit contenir 2 caractères au minimum",
     *      maxMessage = "Le nom que vous rehcerchez doit contenir 50 caractères au minimum"
     * )
     */
    private $nom;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le prénom que vous recherchez doit contenir 2 caractères au minimum",
     *      maxMessage = "Le prénom que vous rehcerchez doit contenir 50 caractères au minimum"
     * )
     */
    private $prenom;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le nom patronymique que vous recherchez doit contenir 2 caractères au minimum",
     *      maxMessage = "Le nom patronymique que vous rehcerchez doit contenir 50 caractères au minimum"
     * )
     */
    private $nomPatronymique; 
    
    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le RNE que vous recherchez doit contenir 2 caractères au minimum",
     *      maxMessage = "Le RNE que vous rehcerchez doit contenir 50 caractères au minimum"
     * )
     */    
    private $rne;

    private $dateAction;

    private $filtre;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom = null): self
    {
        $this->nom = $nom;

        return $this;
    }    

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom = null): self
    {
        $this->prenom = $prenom;

        return $this;
    }    

    public function getNomPatronymique(): ?string
    {
        return $this->nomPatronymique;
    }

    public function setNomPatronymique(string $nomPatronymique = null): self
    {
        $this->nomPatronymique = $nomPatronymique;

        return $this;
    } 

    public function getRne(): ?string
    {
        return $this->rne;
    }

    public function setRne(string $rne = null): self
    {
        $this->rne = $rne;

        return $this;
    }  

    public function getDateAction()
    {
        return $this->dateAction;
    }

    public function setDateAction( $dateAction = null)
    {
        $this->dateAction = $dateAction;

        return $this;
    } 

    public function getFiltre()
    {
        return $this->filtre;
    }

    public function setFiltre($filtre)
    {
        $this->filtre = $filtre;

        return $this;
    }  

}