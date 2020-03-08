<?php

namespace App\Tool;
use App\Entity\Grade;
use App\Tool\Recherche;
use App\Entity\Personnel;
use App\Entity\LieuAffectation;
use Doctrine\ORM\Mapping as ORM;
use Normandie\LdapBundle\Ldap\KLdap;
use Normandie\LdapBundle\Ldap\LdapResult;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Normandie\LdapBundle\Ldap\Interfaces\ILdap;
/**
 * @Route("/auth")
 *
 * @author TLeconte
 *
 */
class LDAP extends KLdap{

    /**
	 * tableau contenant les adresses des diferérent LDAP du fichier .env à condition que l'adresse contient .fr .com
	 * @var array
	 */
    private $tabLdapAdresse;
    private $tabLdapPort;
    private $tabLdapOU;
    private $tabLdapLogin;
    private $tabLdapMdp;
    private $tabLdapDn;

    public function __construct(){
        
        parent::__construct();
        
 
        }

    private function connecter1Ldap(string $ldapAdresse, string $ldapPort) : void {
        $this->connecter($ldapAdresse, $ldapPort);
        switch ($this->getConnexionStatus()) {
            case ILdap::CONNEXION_ERROR :throw new \Exception( "Probleme technique ou login/mdp principal lors de la connexion");
        break;
            case ILdap::CONNEXION_VOID :throw new \Exception( "Aucune tentative de connexion n'a été faite");   
                }
    }
    /**
     * Recherche Simple de plusieurs personnes dans le LDAP uniquement sur le nom (obligatoire) et le prénom (le nom peut etre un patronyme et le prénom est optionnel)
     * 
     * @param $recherche dans src/Tool
	 * @return array tableau d'objets personnel (cf entity)
	 */

    public function recherchePersonnel(Recherche $recherche){
        $this->authentifier($_ENV["NORMANDIE_LDAP_1_LOGIN"], $_ENV["NORMANDIE_LDAP_1_MDP"], $_ENV["NORMANDIE_LDAP_1_OU"]);
        $dn = "ou=personnels EN,".$_ENV["NORMANDIE_LDAP_1_OU"];
        $ldapfieldsResultRequired = array('setNom'=>'surname', 'setPrenom'=>'givenname', 'setNomPatronymique'=>'nompatro', 'setIdLdap'=>'uid', 'setNumen'=>'employeenumber');
        $ldapfieldsResultNotRequired = array('setAffectation'=>'rne','setGrade'=>'grade');
        $relation1nJointure = array('setGrade'=>'setCode','setAffectation'=>'setRne');
        $attributRechercheToAttributFilter=array ('getNom'  => 'sn|nompatro','getPrenom'=>'givenname');
        $ldapResult = new LdapResult();
        
        $filter = $this->addFilter($recherche,$attributRechercheToAttributFilter);
       
        $ldapResult = $this->rechercheLibre($filter, $dn, array_values(array_merge($ldapfieldsResultNotRequired,$ldapfieldsResultRequired)));
        $tabPersonnels=$this->resultLdapToArrayPersonnel($ldapResult,$ldapfieldsResultNotRequired,$ldapfieldsResultRequired, $relation1nJointure);

        return $tabPersonnels;
    }

    /**
     * Recherche Avancé de plusieurs personnes dans le LDAP uniquement sur le nom (obligatoire) et le prénom (le nom peut etre un patronyme et le prénom est optionnel)
     * 
     * @param Recherche $recherche dans src/Tool
	 * @return array tableau d'objets personnel (cf entity)
	 */

    public function rechercheAvancePersonnel(Recherche $recherche){
        $this->authentifier($_ENV["NORMANDIE_LDAP_1_LOGIN"], $_ENV["NORMANDIE_LDAP_1_MDP"], $_ENV["NORMANDIE_LDAP_1_OU"]);
        $dn = "ou=personnels EN,".$_ENV["NORMANDIE_LDAP_1_OU"];
        $ldapfieldsResultRequired = array('setNom'=>'surname', 'setPrenom'=>'givenname', 'setNomPatronymique'=>'nompatro', 'setIdLdap'=>'uid', 'setNumen'=>'employeenumber');
        $ldapfieldsResultNotRequired = array('setAffectation'=>'rne','setGrade'=>'grade');
        $relation1nJointure = array('setGrade'=>'setCode','setAffectation'=>'setRne');
        $attributRechercheToAttributFilter=array ('getNom'  => 'sn|nompatro','getNomPatronymique'  => 'nompatro','getPrenom'=>'givenname','getRne'=>'rne');
        
        $ldapResult = new LdapResult();
        
        $filter = $this->addFilter($recherche,$attributRechercheToAttributFilter);
       
        $ldapResult = $this->rechercheLibre($filter, $dn, array_values(array_merge($ldapfieldsResultNotRequired,$ldapfieldsResultRequired)));
        $tabPersonnels=$this->resultLdapToArrayPersonnel($ldapResult,$ldapfieldsResultNotRequired,$ldapfieldsResultRequired, $relation1nJointure);

        return $tabPersonnels;
    }

    /**
     * Recherche la fiche d'une unique personne dans le LDAP via son Numen
     * 
     * @param string $numen
	 * @return personnel retourne un objet personnel (cf entity)
	 */
    public function chercherUnPersonnel(string $numen){
        $this->authentifier($_ENV["NORMANDIE_LDAP_1_LOGIN"], $_ENV["NORMANDIE_LDAP_1_MDP"], $_ENV["NORMANDIE_LDAP_1_OU"]);
        $dn = "ou=personnels EN,".$_ENV["NORMANDIE_LDAP_1_OU"];
        $ldapfieldsResultRequired = array('setNom'=>'surname', 'setPrenom'=>'givenname', 'setNomPatronymique'=>'nompatro', 'setIdLdap'=>'uid', 'setNumen'=>'employeenumber');
        $ldapfieldsResultNotRequired = array('setAffectation'=>'rne','setGrade'=>'grade');
        $relation1nJointure = array('setGrade'=>'setCode','setAffectation'=>'setRne');
        //$attributsAffiches = array("surname", "givenname", "cn", "uid", "grade", "rne", "employeenumber");
        $ldapResult = new LdapResult();
        
        //$filter = $this->addFilter($recherche,$attributRechercheToAttributFilter);
        $filter = "(employeenumber=".$numen.")";
        $ldapResult = $this->rechercheLibre($filter, $dn, array_values(array_merge($ldapfieldsResultNotRequired,$ldapfieldsResultRequired)));
        $tabPersonnels=$this->resultLdapToArrayPersonnel($ldapResult,$ldapfieldsResultNotRequired,$ldapfieldsResultRequired, $relation1nJointure);
        if(count($tabPersonnels)>1){throw new \Exception( "Impossible deux personnes ont le même Numen");}
        return $tabPersonnels[0];
    }

    /**
     * Créé un filtre LDAP à partir d'un tableau associatif qui met en correspondance la methode get de l'entity et l'atttribut LDAP
     *  Exemple : array ('getNom'  => 'sn|nompatro','getNomPatronymique'  => 'nompatro','getPrenom'=>'givenname','getRne'=>'rne');
     * 
     * @param Recherche $recherche dans src/Tool
     * @param Array $attributRechercheToAttributFilter
	 * @return string $filter retourne un filtre LDAP
	 */
    private function addFilter(Recherche $recherche, Array $attributRechercheToAttributFilter) : string{
        $filter = "(&";
        foreach ($attributRechercheToAttributFilter as $key => $value) {
            // si $key égale au string 'getNom' et value est égale au string 'sn' alors $recherche->$key() correspond  $recherche->getNom()
            // et $filter .= "(".$value."=". $recherche->$key().')' correspond  '$filter .= "(|(sn=".$recherche->getNom().")'
            if($recherche->$key()!=""){
                $tableOu= explode ('|', $value);
                if(count($tableOu)>1){
                    $filterOU='(|';
                    $endFilterOU=')';
                }else 
                {
                    $filterOU='';
                    $endFilterOU='';
                } 
                foreach ($tableOu as $valueTableOu){
                    $filterOU .= "(".$valueTableOu."=". $recherche->$key().')';
                }
                
                $filter .= $filterOU . $endFilterOU;
            }else{
                $filter .=  "(".$value."=*)"; 
            }
        }
         $filter .= ")";
    return $filter;        
    }

    /**
     * Transforme le résultat fourni par LdapResul en un tableau d'objet personnel
     *  
     * les tableaux associatifs ci-dessous associent les setters de l'objet personnel (entity) aux attributs LDAP :
     * @param array $ldapfieldsResultRequired  Exemple : $ldapfieldsResultRequired = array('setNom'=>'surname', 'setPrenom'=>'givenname', 'setNomPatronymique'=>'nompatro', 'setIdLdap'=>'uid', 'setNumen'=>'employeenumber');
     * @param array $ldapfieldsResultNotRequired  Exemple : $ldapfieldsResultNotRequired = array('setAffectation'=>'rne','setGrade'=>'grade');
     * 
     * * le tableau  associatif ci-dessous associe le setter de l'objet personnel (entity) au setters des objets grade et lieuxAffectation (entity)!:
     * @param array $relation1nJointure Exemple $relation1nJointure = array('setGrade'=>'setCode','setAffectation'=>'setRne');
	 * @return string $filter retourne un filtre LDAP
	 */
    private function resultLdapToArrayPersonnel(LdapResult $ldapResult, array $ldapfieldsResultNotRequired ,array $ldapfieldsResultRequired, array $relation1nJointure) : array {
        $personnels = array();
        //$ldapfieldsResultRequired = array('setNom'=>'surname', 'setPrenom'=>'givenname', 'setNomPatronymique'=>'nompatro', 'setIdLdap'=>'uid', 'setNumen'=>'employeenumber');
        //$ldapfieldsResultNotRequired = array('setAffectation'=>'rne','setGrade'=>'grade');
        //$relation1nJointure = array('setGrade'=>'setCode','setAffectation'=>'setRne');
        for($i=0;$i<$ldapResult->getNbFiche();$i++){
            $ficheLdap = $ldapResult->getFiche($i);
            $personnel = new Personnel();
            $grade = new Grade();
            $rne = new LieuAffectation();
            $flagPersonnelComplete=true;

            foreach ($ldapfieldsResultRequired as $key => $value) {
                if(isset($ficheLdap[$value])){$personnel->$key($ficheLdap[$value]);}
                else {
                    $flagPersonnelComplete=false;
                    break;
                }
            }   
            foreach ($ldapfieldsResultNotRequired as $key => $value) {
            if(isset($ficheLdap[$value])){
                if(array_key_exists ($key , $relation1nJointure)){
                    $relationMethode = $relation1nJointure[$key]; //exemple<=> setCode; setRne
                    $$value->$relationMethode($ficheLdap[$value]); // exemple <=> $grade->setCode($ficheLdap["grade"]); $rne->setRne($ficheLdap["rne"]);
                    $personnel->$key($$value); //exemple <=>$personnel->setGrade($grade)
                }
                else {$personnel->$key($ficheLdap[$value]);}
             }

            else {
                $flagPersonnelComplete=false;
                break;
                
                }
            }
            if ($flagPersonnelComplete) {
                $personnel->setSource("LDAP");
                array_push($personnels, $personnel);
            }
        }
        return $personnels;


    }




}

?>