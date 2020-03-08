<?php

namespace App\Controller;

use App\Tool\LDAP;
use App\Entity\Grade;
use App\Tool\Recherche;
use App\Entity\Personnel;
use App\Form\RechercheType;
use App\Entity\LieuAffectation;
use App\Repository\GradeRepository;
use App\Tool\Interfaces\IRecherche;
use App\Form\RecherchePersonnelType;
use App\Repository\ActionRepository;
use Normandie\CsvBundle\Csv\CsvWriter;
use App\Repository\PersonnelRepository;
use App\Repository\LieuAffectationRepository;
use Symfony\Component\HttpFoundation\Request;
use Normandie\LdapBundle\Ldap\Interfaces\ILdap;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/recherche")
 * @Security("is_granted('ROLE_RECHERCHE')")
 */
class RechercheController extends AbstractController
{
    /**
     * @Route("/", name="recherche_index")
     * @Security("is_granted('ROLE_RECHERCHE')")
     */
    public function index()
    {
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
        ]);
    }

    /**
     * @Route("/personnel", name="recherche_personnel", methods={"GET","POST"})
     * @Security("is_granted('ROLE_LECTURE')")
     * 
     * Recherche simple renvoyant un tableau de personnel.
     */
    public function rechercherPersonnel(Request $request, PersonnelRepository $personnelRepository, ActionRepository $actionRepository){
        $recherche = new Recherche();
        $form = $this->createForm(RechercheType::class, $recherche);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

        	if ($form->isValid()){

                $caracteresInterdits = array("**", "!", "&", "|", "(", ")", "=", "<", ">");

                for($i=0; $i<sizeof($caracteresInterdits);$i++){
                    $recherche->setNom(str_replace($caracteresInterdits[$i], "", $recherche->getNom()));
                    $recherche->setPrenom(str_replace($caracteresInterdits[$i], "", $recherche->getPrenom()));
                }

                $filtreCET = $recherche->getFiltre();

                if($filtreCET == IRecherche::CET_OUVERT){
                    //SI ON RECHERCHE LES PERSONNES AVEC CET OUVERT, ON VA EN BDD
                    $personnels = $personnelRepository->rechercherPersonnel($recherche);
                    $personnels = $personnelRepository->arrayToObject($personnels);
                }else if($filtreCET == IRecherche::CET_FERME){
                    //SI ON RECHERCHE LES PERSONNES QUI N'ONT PAS DE CET, ON VA EN LDAP
                    $ldap = new LDAP();
                    $ldap->connecter($_ENV["NORMANDIE_LDAP_1_ADRESSE"], $_ENV["NORMANDIE_LDAP_1_PORT"]);
                    if ($ldap->getConnexionStatus() == ILdap::CONNEXION_OK){ //2 = LOGIN CORRECT
                        $personnels = $ldap->recherchePersonnel($recherche);
                        //ON CHERCHE LE GRADE ET L'AFFECTATION CORRESPONDANTE A CHAQUE PERSONNE
                        for($i=0; $i<sizeof($personnels);$i++){
                            //ON VERIFIE DANS LA BDD QUE LA PERSONNE TROUVE N'A PAS UN COMPTE DEJA OUVERT
                            $repository = $this->getDoctrine()->getRepository(Personnel::class);
                            $resultat = $repository->findOneByNumen($personnels[$i]->getNumen());
                            if(!empty($resultat)){
                                unset($personnels[$i]);
                                sort($personnels);
                                continue;
                            }else{
                                
                                //RECHERCHE DU GRADE CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(Grade::class);
                                if(!empty($personnels[$i]->getGrade())){
                                    $grade = $repository->findOneByCode($personnels[$i]->getGrade()->getCode());
                                    if(!empty($grade)){
                                        $personnels[$i]->setGrade($grade);
                                    }
                                }

                                //RECHERCHE DU LIEU D'AFFECTATION CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
                                //die(var_dump($personnels[$i]->getAffectation()));
                                if(!empty($personnels[$i]->getAffectation())){
                                    $lieuAffectation = $repository->findOneByRne($personnels[$i]->getAffectation()->getRne());
                                    if(!empty($lieuAffectation)){
                                        $personnels[$i]->setAffectation($lieuAffectation);
                                    }
                                }
                            }                            
                        }
                        //die(var_dump($personnels[0]));
                    }

                }else{
                    //SI ON RECHERCHE LES PERSONNES AVEC CET OUVERT OU PAS, D'ABORD LDAP ENSUITE BDD
                    $personnels = array();

                    $ldap = new LDAP();
                    $ldap->connecter($_ENV["NORMANDIE_LDAP_1_ADRESSE"], $_ENV["NORMANDIE_LDAP_1_PORT"]);
                    if ($ldap->getConnexionStatus() == ILdap::CONNEXION_OK){ //2 = LOGIN CORRECT
                        $personnelLdap = $ldap->recherchePersonnel($recherche);
                        //ON CHERCHE LE GRADE ET L'AFFECTATION CORRESPONDANTE A CHAQUE PERSONNE
                        for($i=0; $i<sizeof($personnelLdap);$i++){
                            //ON VERIFIE DANS LA BDD QUE LA PERSONNE TROUVE N'A PAS UN COMPTE DEJA OUVERT
                            $repository = $this->getDoctrine()->getRepository(Personnel::class);
                            $resultat = $repository->findOneByNumen($personnelLdap[$i]->getNumen());
                            //die(var_dump($resultat));
                            if(is_object($resultat)){
                                unset($personnelLdap[$i]);
                                sort($personnelLdap);
                                continue;
                            }else{
                                
                                //RECHERCHE DU GRADE CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(Grade::class);
                                if(!empty($personnelLdap[$i]->getGrade())){
                                    $grade = $repository->findOneByCode($personnelLdap[$i]->getGrade()->getCode());
                                    if(!empty($grade)){
                                        $personnelLdap[$i]->setGrade($grade);
                                    }
                                }

                                //RECHERCHE DU LIEU D'AFFECTATION CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
                                //die(var_dump($personnels[$i]->getAffectation()));
                                if(!empty($personnelLdap[$i]->getAffectation())){
                                    $lieuAffectation = $repository->findOneByRne($personnelLdap[$i]->getAffectation()->getRne());
                                    if(!empty($lieuAffectation)){
                                        $personnelLdap[$i]->setAffectation($lieuAffectation);
                                    }
                                }
                            }                            
                        }
                    }

                    $personnelBddArray = $personnelRepository->rechercherPersonnel($recherche);  
                    $personnelBdd = $personnelRepository->arrayToObject($personnelBddArray);
                    $personnels = array_merge($personnelLdap, $personnelBdd);

                }               
                return $this->render('recherche/resultatRecherche.html.twig', [
                    'personnels' => $personnels,
                ]);  

            }
            else{
				$this->addFlashErreur("Erreur lors de la recherche...");
        	}    
        }
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/personnel/avance", name="recherche_avance_personnel", methods={"GET","POST"})
     * @Security("is_granted('ROLE_LECTURE')")
     * 
     * Recherche avancée renvoyant un tableau de personnel.
     */
    public function rechercheAvancePersonnel(Request $request, PersonnelRepository $personnelRepository){
        $recherche = new Recherche();
        $form = $this->createForm(RechercheType::class, $recherche);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

        	if ($form->isValid()){

                //die(var_dump($personnelRepository->findAllGreaterThanPrice()));
                //die(var_dump($recherche->getDateAction()));

                $caracteresInterdits = array("**", "!", "&", "|", "(", ")", "=", "<", ">");

                for($i=0; $i<sizeof($caracteresInterdits);$i++){
                    $recherche->setNom(str_replace($caracteresInterdits[$i], "", $recherche->getNom()));
                    $recherche->setNomPatronymique(str_replace($caracteresInterdits[$i], "", $recherche->getNomPatronymique()));
                    $recherche->setPrenom(str_replace($caracteresInterdits[$i], "", $recherche->getPrenom()));
                    $recherche->setRne(str_replace($caracteresInterdits[$i], "", $recherche->getRne()));
                }

                $filtreCET = $recherche->getFiltre();

                if($filtreCET == IRecherche::CET_OUVERT){
                    //SI ON RECHERCHE LES PERSONNES AVEC CET OUVERT, ON VA EN BDD
                    $personnels = $personnelRepository->rechercheAvancePersonnel($recherche);
                    $personnels = $personnelRepository->arrayToObject($personnels);
                }else if($filtreCET == IRecherche::CET_FERME){
                    //SI ON RECHERCHE LES PERSONNES QUI N'ONT PAS DE CET, ON VA EN LDAP
                    $ldap = new LDAP();
                    $ldap->connecter($_ENV["NORMANDIE_LDAP_1_ADRESSE"], $_ENV["NORMANDIE_LDAP_1_PORT"]);
                    if ($ldap->getConnexionStatus() == ILdap::CONNEXION_OK){ //2 = LOGIN CORRECT
                        $personnels = $ldap->rechercheAvancePersonnel($recherche);
                        //ON CHERCHE LE GRADE ET L'AFFECTATION CORRESPONDANTE A CHAQUE PERSONNE
                        for($i=0; $i<sizeof($personnels);$i++){
                            //ON VERIFIE DANS LA BDD QUE LA PERSONNE TROUVE N'A PAS UN COMPTE DEJA OUVERT
                            $repository = $this->getDoctrine()->getRepository(Personnel::class);
                            $resultat = $repository->findOneByNumen($personnels[$i]->getNumen());
                            if(!empty($resultat)){
                                unset($personnels[$i]);
                                sort($personnels);
                                continue;
                            }else{
                                
                                //RECHERCHE DU GRADE CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(Grade::class);
                                if(!empty($personnels[$i]->getGrade())){
                                    $grade = $repository->findOneByCode($personnels[$i]->getGrade()->getCode());
                                    if(!empty($grade)){
                                        $personnels[$i]->setGrade($grade);
                                    }
                                }

                                //RECHERCHE DU LIEU D'AFFECTATION CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
                                //die(var_dump($personnels[$i]->getAffectation()));
                                if(!empty($personnels[$i]->getAffectation())){
                                    $lieuAffectation = $repository->findOneByRne($personnels[$i]->getAffectation()->getRne());
                                    if(!empty($lieuAffectation)){
                                        $personnels[$i]->setAffectation($lieuAffectation);
                                    }
                                }
                            }                            
                        }
                        //die(var_dump($personnels[0]));
                    }

                }else{
                    //SI ON RECHERCHE LES PERSONNES AVEC CET OUVERT OU PAS, D'ABORD LDAP ENSUITE BDD
                    $personnels = array();

                    $ldap = new LDAP();
                    $ldap->connecter($_ENV["NORMANDIE_LDAP_1_ADRESSE"], $_ENV["NORMANDIE_LDAP_1_PORT"]);
                    if ($ldap->getConnexionStatus() == ILdap::CONNEXION_OK){ //2 = LOGIN CORRECT
                        $personnelLdap = $ldap->rechercheAvancePersonnel($recherche);
                        //ON CHERCHE LE GRADE ET L'AFFECTATION CORRESPONDANTE A CHAQUE PERSONNE
                        for($i=0; $i<sizeof($personnelLdap);$i++){
                            //ON VERIFIE DANS LA BDD QUE LA PERSONNE TROUVE N'A PAS UN COMPTE DEJA OUVERT
                            $repository = $this->getDoctrine()->getRepository(Personnel::class);
                            $resultat = $repository->findOneByNumen($personnelLdap[$i]->getNumen());
                            //die(var_dump($resultat));
                            if(is_object($resultat)){
                                unset($personnelLdap[$i]);
                                sort($personnelLdap);
                                continue;
                            }else{
                                //RECHERCHE DU GRADE CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(Grade::class);
                                if(!empty($personnelLdap[$i]->getGrade())){
                                    $grade = $repository->findOneByCode($personnelLdap[$i]->getGrade()->getCode());
                                    if(!empty($grade)){
                                        $personnelLdap[$i]->setGrade($grade);
                                    }
                                }

                                //RECHERCHE DU LIEU D'AFFECTATION CORRESPONDANT
                                $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
                                //die(var_dump($personnels[$i]->getAffectation()));
                                if(!empty($personnelLdap[$i]->getAffectation())){
                                    $lieuAffectation = $repository->findOneByRne($personnelLdap[$i]->getAffectation()->getRne());
                                    if(!empty($lieuAffectation)){
                                        $personnelLdap[$i]->setAffectation($lieuAffectation);
                                    }
                                }
                            }                            
                        }
                    }

                    $personnelBdd = $personnelRepository->rechercheAvancePersonnel($recherche);
                    $personnelBdd = $personnelRepository->arrayToObject($personnelBdd);
                    $personnels = array_merge($personnelLdap, $personnelBdd);
                }               

                return $this->render('recherche/resultatRecherche.html.twig', [
                    'personnels' => $personnels,
                ]);  

            }
            else{
				$this->addFlashErreur("Erreur lors de la recherche...");
        	}    
        }
        return $this->render('recherche/avance/index.html.twig', [
            'controller_name' => 'RechercheController',
            'form' => $form->createView(),
        ]);
    }
}
