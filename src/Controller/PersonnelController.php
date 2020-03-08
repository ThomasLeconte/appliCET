<?php

namespace App\Controller;

use DateTime;
use App\Tool\LDAP;
use App\Entity\Grade;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Action;
use App\Entity\Personnel;
use App\Entity\TypeAction;
use App\Form\PersonnelType;
use App\Entity\LieuAffectation;
use App\Form\PersonnelModifType;
use App\Repository\ActionRepository;
use App\Tool\Interfaces\IDestination;
use Normandie\CsvBundle\Csv\CsvWriter;
use App\Repository\PersonnelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb

/**
 * @Route("/personnel")
 * 
 */
class PersonnelController extends BaseController
{
    /**
     * @Route("/", name="personnel_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(PersonnelRepository $personnelRepository, ActionRepository $actionRepository): Response
    {
        $csvWriter = new CsvWriter();
        $arrayExport = $personnelRepository->arrayForExportCsv($actionRepository);
        $csvWriter->writeDataToCsvFile($arrayExport, IDestination::EXPORT_CSV."Export-Recherche.csv", ";");
        //Cette méthode ne renvoie pas de données, mais dans le doute voilà l'ancienne ligne : 
        //$fichier = $csvWriter->writeDataToCsvFile($arrayExport, IDestination::EXPORT_CSV."Export-Recherche.csv", ";");

        return $this->render('personnel/index.html.twig', [
            'personnels' => $personnelRepository->findAll(),
            'test' => $_ENV['NORMANDIE_VIEW_NOM_ACADEMIE'],
        ]);
    }

    /**
     * @Route("/new", name="personnel_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $personnel->setNomPatronymique($personnel->getNom());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personnel);
                $entityManager->flush();

    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('personnel_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('personnel/new.html.twig', [
            'personnel' => $personnel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/{numen}", name="personnel_newByNumen", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE_LDAP')")
     * 
     * Permet de récupérer le profil LDAP de quelqu'un grâce à son numen et d'afficher le tout
     * dans un formulaire, qui peut l'instancier en base.
     */
    public function newPersonnel(Request $request, string $numen): Response
    {
        $personnel = new Personnel();
        $ldap = new LDAP();
        $ldap->connecter($_ENV["NORMANDIE_LDAP_1_ADRESSE"], $_ENV["NORMANDIE_LDAP_1_PORT"]);
        if ($ldap->getConnexionStatus() == 2){ //2 = LOGIN CORRECT
            $personnel = $ldap->chercherUnPersonnel($numen);
            //ON CHERCHE LE GRADE CORRESPONDANT
            $repository = $this->getDoctrine()->getRepository(Grade::class);
            $grade = $repository->findOneByCode($personnel->getGrade()->getCode());
            $personnel->setGrade($grade);

            //ON CHERCHE LE LIEU D'AFFECTATION CORRESPONDANT
            $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
            $lieuAffectation = $repository->findOneByRne($personnel->getAffectation()->getRne());
            $personnel->setAffectation($lieuAffectation);
        }
        
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $personnel->setNomPatronymique($personnel->getNom());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personnel);
                $entityManager->flush();

                //ON OUVRE LE COMPTE CET AVEC UNE NOUVELLE ACTION "Ouverture CET" :
                $action = new Action();
                $now = new DateTime();
                $repository = $this->getDoctrine()->getRepository(TypeAction::class);
                $typeAction = $repository->findOneByLibelle("Ouverture CET");

                $action->setAnnee($now->format('Y'));
                $action->setDate($now);
                $action->setJours(0);
                $action->setConges(0);
                $action->setTypeAction($typeAction);
                $action->setPersonnel($personnel);

                $entityManager->persist($action);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('personnel_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('personnel/new.html.twig', [
            'personnel' => $personnel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{numen}", name="personnel_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(Personnel $personnel, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }

    /**
     * @Route("/{numen}/edit", name="personnel_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, Personnel $personnel, string $numen): Response
    {

        $this->checkCSRF($request);
        
        $form = $this->createForm(PersonnelModifType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('personnel_index');
        }

        return $this->render('personnel/edit.html.twig', [
            'personnel' => $personnel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{numen}", name="personnel_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, Personnel $personnel): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($personnel);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('personnel_index');
    }

}
