<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Tool\Import;
use App\Form\DataType;
use App\Form\ImportType;
use App\Entity\LieuAffectation;
use App\Tool\Interfaces\IDestination;
use Normandie\CsvBundle\Csv\CsvReader;
use Symfony\Component\HttpFoundation\Request;
use Normandie\CsvBundle\Csv\CsvReaderException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

define('DESTINATION', '../temp/csv');
/**
 * @Route("/data")
 * @Security("is_granted('ROLE_IMPORTATION_DONNEES')")
 */
class DataController extends AbstractController
{
    /**
     * @Route("/", name="data_index")
     */
    public function index()
    {
        $etablissements = "";
        $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);
        $cpt = sizeof($repository->findAll());
        if($cpt == 0){
            $etablissements = "Vide";
        }else{
            $etablissements = "Rempli";
        }

        return $this->render('data/index.html.twig', [
            'etablissements' => $etablissements
        ]);
    }

    /**
     * @Route("/hydraterEtablissements", name="data_hydraterEtablissements")
     * @Security("is_granted('ROLE_ECRITURE')")
     * 
     * Permet d'hydrater les établissements de l'application en important un fichier CSV.
     */
    public function hydraterEtablissements(Request $request){

        // Suppression de tout les fichiers CSV qui seraient encore présent dans le dossier temp/csv 
        array_map('unlink', glob(IDestination::IMPORT_CSV.'*.csv'));

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $csvReader = new CsvReader();

                if(is_object($import->getFile())){
                    $nomFichier = $import->getFile()->getClientOriginalName();
                    $repository = $this->getDoctrine()->getRepository(LieuAffectation::class);

                    //retourne le template avec une erreur si le fichier n'est pas un csv,
                    //et si c'est un csv, il le deplace dans le dossier temp de l'application
                    $this->checkUploadedFile(1, $import, $form);
                    //Traitement du fichier csv
                    $etablissements = $this->arrayFromCsv(1, $form, $nomFichier, $csvReader);

                    for($i=0;$i<count($etablissements);$i++){
    
                        $lieu = $repository->findOneByRne($etablissements[$i][0]);
                        if(!empty($lieu)){
                            continue;
                        }else{
                            if($i == (count($etablissements)-1)){ //ON ENLEVE LA DERNIERE LIGNE PUISQU'ELLE EST VIDE.
                                continue;
                            }else{
                                $lieu = new LieuAffectation();
                                $lieu->setRne($etablissements[$i][0]);
                                $lieu->setSecteur($etablissements[$i][1]);
                                $lieu->setSigle(trim($etablissements[$i][2]));   //ON ENLEVE LES ESPACES EN TROP
                                $lieu->setLibelle(trim($etablissements[$i][3])); //ON ENLEVE LES ESPACES EN TROP
                                $lieu->setLocalite($etablissements[$i][4]);
                                $lieu->setTypeEtablissement($etablissements[$i][7]);
                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($lieu);
                                $entityManager->flush();
                            }
                        }
                    }
                    //SUPPRESSION DU FICHIER IMPORTé
                    unlink(DESTINATION.$nomFichier);
    
                    return $this->render('data/hydraterEtablissements.html.twig', [
                        'erreur' => "Table 'lieu_affectation' hydratée !"
                    ]); 
                }else{
                    return $this->render('data/hydraterEtablissements.html.twig', [
                        'erreur' => "Veuillez importer un fichier !"
                    ]); 
                }
            }
        }

        return $this->render('data/hydraterEtablissements.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/hydraterGrades", name="data_hydraterGrades")
     * @Security("is_granted('ROLE_ECRITURE')")
     * 
     * Permet d'hydrater les grades de l'application en important un fichier CSV.
     */
    public function hydraterGrades(Request $request){

        // Suppression de tout les fichiers CSV qui seraient encore présent dans le dossier temp/csv 
        array_map('unlink', glob(IDestination::IMPORT_CSV.'*.csv'));

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $csvReader = new CsvReader();

                if(is_object($import->getFile())){
                    $nomFichier = $import->getFile()->getClientOriginalName();
                    $repository = $this->getDoctrine()->getRepository(Grade::class);

                    //retourne le template avec une erreur si le fichier n'est pas un csv,
                    //et si c'est un csv, il le deplace dans le dossier temp de l'application
                    $this->checkUploadedFile(2, $import, $form);
                    //Traitement du fichier csv
                    $grades = $this->arrayFromCsv(2, $form, $nomFichier, $csvReader);
                    
                    for($i=0;$i<count($grades);$i++){
                        $grade = $repository->findOneByCode(trim($grades[$i][0]));
                        if(!empty($grade)){
                            continue;
                        }else{
                            $grade = new Grade();
                            $grade->setCode(trim($grades[$i][0]));          //ON ENLEVE LES ESPACES EN TROP
                            $grade->setLibelleCourt(trim($grades[$i][1]));  //ON ENLEVE LES ESPACES EN TROP
                            $grade->setLibelleLong(trim($grades[$i][2]));   //ON ENLEVE LES ESPACES EN TROP
                            $grade->setCategorie(trim($grades[$i][3]));     //ON ENLEVE LES ESPACES EN TROP
                            
                            if($grades[$i][3] != "(null)"){
                                $grade->setCategorie(trim($grades[$i][3]));
                            }else{
                                $grade->setCategorie(null);
                            }
                            if($grades[$i][4] != "(null)"){
                                $grade->setActif(trim($grades[$i][4]));
                            }else{
                                $grade->setActif(null);
                            }
                            
        
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($grade);
                            $entityManager->flush();
                        }
                    }
                    
                    //SUPPRESSION DU FICHIER IMPORTé
                    unlink(IDestination::IMPORT_CSV.$nomFichier);
                    return $this->render('data/hydraterGrades.html.twig', [
                        'erreur' => "Table 'grade' hydratée !"
                    ]);
                }else{
                    return $this->render('data/hydraterGrades.html.twig', [
                        'erreur' => "Veuillez importer un fichier !"
                    ]); 
                }
            }
        }

        return $this->render('data/hydraterGrades.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Vérifie que le fichier importé est un fichier CSV, et déplace ce fichier temporaire dans le dossier
     * /temp/csv de l'application.
     *
     * @param integer $typeImport
     * @param Import $import
     * @param [type] $form
     * @return void
     */
    public function checkUploadedFile(int $typeImport, Import $import, $form){

        //SI typeImport = 1, alors c'est un import d'établissements
        if($typeImport == 1){
            $infosFichier = pathinfo($import->getFile()->getClientOriginalName());
            if($infosFichier['extension'] != 'csv'){
                return $this->render('data/hydraterEtablissements.html.twig', [
                    'form' => $form->createView(),
                    'erreur' => 'Veuillez importer uniquement des fichiers CSV'
                ]);
            }

            $nomFichier = $import->getFile()->getClientOriginalName();
            $tempNomFichier = $import->getFile()->getRealPath();

            move_uploaded_file($tempNomFichier, IDestination::IMPORT_CSV.$nomFichier);

        }else{
        //Sinon, c'est un import de grades
            $infosFichier = pathinfo($import->getFile()->getClientOriginalName());
            if($infosFichier['extension'] != 'csv'){
                return $this->render('data/hydraterGrades.html.twig', [
                    'form' => $form->createView(),
                    'erreur' => 'Veuillez importer uniquement des fichiers CSV'
                ]);
            }
            $nomFichier = $import->getFile()->getClientOriginalName();
            $tempNomFichier = $import->getFile()->getRealPath();
            move_uploaded_file($tempNomFichier, IDestination::IMPORT_CSV.$nomFichier);
        }
    }

    /**
     * Renvoie un tableau de données à partir d'un fichier CSV, ou une exception s'il y a une erreur.
     *
     * @param integer $typeImport
     * @param [type] $form
     * @param string $nomFichier
     * @param CsvReader $csvReader
     * @return void
     */
    public function arrayFromCsv(int $typeImport, $form, string $nomFichier, CsvReader $csvReader){
        $tableau = array();
        if($typeImport == 1){
            try{
                $tableau = $csvReader->getCsvFromFichier(IDestination::IMPORT_CSV.$nomFichier, $premiereLigne = false, $detecterEncodage = true);
            }catch(CsvReaderException $e){
                return $this->render('data/hydraterEtablissements.html.twig', [
                    'form' => $form->createView(),
                    'erreur' => str_replace("CsvReader :", "", $e->getMessage())
                ]);
            }
        }else{
            try{
                $tableau = $csvReader->getCsvFromFichier(IDestination::IMPORT_CSV.$nomFichier, $premiereLigne = false, $detecterEncodage = true);
            }catch(CsvReaderException $e){
                return $this->render('data/hydraterGrades.html.twig', [
                    'form' => $form->createView(),
                    'erreur' => str_replace("CsvReader :", "", $e->getMessage())
                ]);
            }
        }
        return $tableau;

    }
}
