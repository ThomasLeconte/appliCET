<?php

namespace App\Repository;

use DateTime;
use App\Entity\Grade;
use App\Tool\Recherche;
use App\Entity\Personnel;
use App\Tool\Interfaces\ICet;
use App\Entity\LieuAffectation;
use App\Repository\GradeRepository;
use App\Tool\Interfaces\IRecherche;
use App\Repository\ActionRepository;
use App\Repository\LieuAffectationRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Personnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnel[]    findAll()
 * @method Personnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnel::class);
    }

    /**
     * permet de retourner un tableau de personnels en personnalisant la requête SQL avec le nom,
     * le prénom et l'état de cloture.
     *
     * @param string $nom
     * @param string $prenom
     * @param string $etatCloture
     * @return void
     */
    public function rechercherPersonnelCloture(string $nom, string $prenom, string $etatCloture){

        if($etatCloture == "aucun"){
            return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :nom')
            ->andWhere('p.prenom LIKE :prenom')
            ->setParameter('nom', "%".$nom."%")
            ->setParameter('prenom', "%".$prenom."%")
            ->getQuery()
            ->getResult();
        }else{
            return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :nom')
            ->andWhere('p.prenom LIKE :prenom')
            ->andWhere('p.etatCloture = :etatCloture')
            ->setParameter('nom', "%".$nom."%")
            ->setParameter('prenom', "%".$prenom."%")
            ->setParameter('etatCloture', $etatCloture)
            ->getQuery()
            ->getResult();
        }
    }

    /**
     * Permet de retourner un tableau de données du personnel en fonction des critères de recherche
     * reçus par l'objet $recherche (recherche SIMPLE)
     *
     * @param Recherche $recherche
     * @return void
     */
    public function rechercherPersonnel(Recherche $recherche){

        $recherche->setNom(str_replace("*", "%", $recherche->getNom()));
        $recherche->setPrenom(str_replace("*", "%", $recherche->getPrenom()));
        //die(var_dump($recherche));

        if($recherche->getNom() === ""){
            $personnels = $this->createQueryBuilder('p')
            ->where('p.prenom LIKE :prenom')
            ->setParameter('prenom', $recherche->getPrenom())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $personnels;
        }else if($recherche->getPrenom() ===""){
            $personnels = $this->createQueryBuilder('p')
            ->where('p.nom LIKE :nom OR p.nomPatronymique LIKE :nom')
            ->setParameter('nom', $recherche->getNom())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $personnels;      
        }else{
            $personnels = $this->createQueryBuilder('p')
            ->where('p.nom LIKE :nom')
            ->andWhere('p.prenom LIKE :prenom')
            ->setParameter('nom', $recherche->getNom())
            ->setParameter('prenom', $recherche->getPrenom())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $personnels;
        }
    }

    /**
     * Permet de retourner un tableau de données du personnel en fonction des critères de recherche
     * reçus par l'objet $recherche (recherche AVANCÉE)
     *
     * @param Recherche $recherche
     * @return void
     */
    public function rechercheAvancePersonnel(Recherche $recherche){

        $filtreCloture = "";
        $dateAction = "";

        if(stristr($recherche->getNom(), "*") === FALSE){
            $recherche->setNom("%".$recherche->getNom()."%");
        }else{
            $recherche->setNom(str_replace("*", "%", $recherche->getNom()));
        }

        if(stristr($recherche->getPrenom(), "*") === FALSE){
            $recherche->setPrenom("%".$recherche->getPrenom()."%");
        }else{
            $recherche->setPrenom(str_replace("*", "%", $recherche->getPrenom()));
        }
        
        if(stristr($recherche->getNomPatronymique(), "*") === FALSE){
            $recherche->setNomPatronymique("%".$recherche->getNomPatronymique()."%");
        }else{
            $recherche->setNomPatronymique(str_replace("*", "%", $recherche->getNomPatronymique()));
        }

        if(stristr($recherche->getRne(), "*") === FALSE){
            if(strlen($recherche->getRne()) != 8){
                $recherche->setRne("%".$recherche->getRne()."%");
            }
        }else{
            $recherche->setRne(str_replace("*", "%", $recherche->getRne()));
        }

        if($recherche->getFiltre() == IRecherche::CET_NUL){
            $filtreCloture = "%%";
        }else{
            if($recherche->getFiltre() == IRecherche::CET_OUVERT){
                $filtreCloture = "%0%";
            }else{
                $filtreCloture = "%1%";
            }
        }

        if($recherche->getDateAction()!= null){
            $dateAction = $recherche->getDateAction()->format('Y-m-d H:i:s');
        }

        if($recherche->getNomPatronymique() ==="%%" && $recherche->getNom() != "%%"){
            $recherche->setNomPatronymique($recherche->getNom());
        }

        //die(var_dump($recherche));

        $conn = $this->getEntityManager()->getConnection();
        //SI AUCUN RNE N'EST RENSEIGNÉ
        if($recherche->getRne() === "%%"){
            
            if($recherche->getDateAction()!=null){
                
                $sql = '
                SELECT DISTINCT * FROM personnel p, action a
                WHERE p.numen = a.personnel
                AND p.nom LIKE :nom
                AND p.prenom LIKE :prenom
                AND p.etat_cloture LIKE :etatCloture
                AND a.date < :dateAction
                OR p.nom_patronymique LIKE :nompatro
                ';      
                $stmt = $conn->prepare($sql);
                $stmt->execute(['nom' => $recherche->getNom(),
                                'nompatro' => $recherche->getNomPatronymique(),
                                'prenom' => $recherche->getPrenom(),
                                'etatCloture' => $filtreCloture,
                                'dateAction' => $dateAction]);  
            }else{
                $sql = '
                SELECT * FROM personnel p
                WHERE p.nom LIKE :nom
                AND p.prenom LIKE :prenom
                AND p.etat_cloture LIKE :etatCloture
                OR p.nom_patronymique LIKE :nompatro
                ';      
                $stmt = $conn->prepare($sql);
                $stmt->execute(['nom' => $recherche->getNom(),
                                'nompatro' => $recherche->getNomPatronymique(),
                                'prenom' => $recherche->getPrenom(),
                                'etatCloture' => $filtreCloture]);  
            }
    
        }else{
            if($recherche->getDateAction()!=null){
                
                $sql = '
                SELECT DISTINCT * FROM personnel p, action a
                WHERE p.numen = a.personnel
                AND p.nom LIKE :nom
                AND p.prenom LIKE :prenom
                AND p.lieu_affectation LIKE :rne
                AND p.etat_cloture LIKE :etatCloture
                AND a.date < :dateAction
                OR p.nom_patronymique LIKE :nompatro
                ';      
                $stmt = $conn->prepare($sql);
                $stmt->execute(['nom' => $recherche->getNom(),
                                'nompatro' => $recherche->getNomPatronymique(),
                                'prenom' => $recherche->getPrenom(),
                                'rne' => $recherche->getRne(),
                                'etatCloture' => $filtreCloture,
                                'dateAction' => $dateAction]);  
            }else{
                $sql = '
                SELECT DISTINCT * FROM personnel p
                WHERE p.nom LIKE :nom
                AND p.prenom LIKE :prenom
                AND p.lieu_affectation LIKE :rne
                AND p.etat_cloture LIKE :etatCloture
                OR p.nom_patronymique LIKE :nompatro
                ';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['nom' => $recherche->getNom(),
                                'nompatro' => $recherche->getNomPatronymique(),
                                'prenom' => $recherche->getPrenom(),
                                'rne' => $recherche->getRne(),
                                'etatCloture' => $filtreCloture]);
            }

        }
        // returns an array of arrays (i.e. a raw data set)
        $resultat = $stmt->fetchAll();
        return $resultat;

    }

    /**
     * Permet de retourner un tableau avec un bilan annuel des personnes retournés par la recherche simple ou avancée
     *
     * @param ActionRepository $actionRepository
     * @return void
     */
    public function arrayForExportCsv(ActionRepository $actionRepository){
        $bilan = array();
        $personnelArray = $this->findAll();

        $date = new DateTime();
        $year = intval($date->format('Y'))-1;
        $champsAffiches = array('Numen', 'Nom / Prenom', 'Nom Patronymique', 'Grade', 'Etablissement', 'Ville', 'Ouverture CET', 'Solde '.$year, 'Nb jours verses '.$year, 'Nb jours indemnises '.$year, 'Nb jours consommés en conges '.$year, 'Nb jours consommes en RAFP '.$year, 'Mouvement CET '.$year);
        
        for($i=0;$i<sizeof($personnelArray);$i++){
            $personnel = $personnelArray[$i];
            $ligne = array();

            /*==================INFOS PERSONNEL===================*/
            $ligne[0] = $personnel->getNumen();
            $ligne[1] = $personnel->getNom()." ".$personnel->getPrenom();
            $ligne[2] = $personnel->getNomPatronymique();
            if(is_object($personnel->getGrade())){
                $ligne[3] = $personnel->getGrade()->getLibelleCourt();
            }else{
                $ligne[3] = "Non defini";
            }
            if(is_object($personnel->getAffectation())){
                $ligne[4] = $personnel->getAffectation()->getLibelle();
                $ligne[5] = $personnel->getAffectation()->getLocalite();
            }else{
                $ligne[4] = "Non defini";
                $ligne[5] = "Non defini";
            }
            /*================INFOS CET===================*/
            $ligne[6] = $actionRepository->getDateOuvertureCET($personnel->getNumen());
            $actions = $actionRepository->findbyPersonnelAndAnnee($personnel->getNumen(), $year);
            
            if(sizeof($actions)>0){
                $soldeJoursCET = 0;
                $actionSuivante = 0;
                for($j=0;$j<sizeof($actions);$j++){
                    $annee = $actions[$j]->getAnnee();
                    $jours = strval($actions[$j]->getJours());
                    $conges = strval($actions[$j]->getConges());
                    $nbJoursIndemnises = 0;
                    $nbJoursConges = 0;
                    $nbJoursRAFP = 0;
                    $mouvementCET = 0;

                            
                    if($actionSuivante == $actions[$j]->getId()){
                        $actionSuivante = 0;
                        continue;
                    }
        
                    //CONTROLE SI L'ACTION SUIVANTE EST DE LA MÊME ANNÉE QUE CETTE ACTION
                    if($j>0 && $j+1<count($personnel->getActions()) && $actions[$j+1]->getAnnee() == $actions[$j]->getAnnee()){
                        $actionSuivante = $actions[$j+1]->getId();
                        switch($actions[$j+1]->getTypeAction()->getLibelle()){
                            //PAS D'OUVERTURE ET D'EPARGNE, PUISQUE L'ACTION SUIVANTE NE PEUT PAS ÊTRE UNE OUVERTURE PUISQUE LE COMPTE EST DEJA OUVERT
                            //ET NE PEUT PAS ETRE "EPARGNER" PUISQU'ON EPARGNE 1 FOIS DANS L'ANNEE
                            case ICet::CET_CONGES :
                                $nbJoursConges += $jours;
                                $mouvementCET = 0-$nbJoursConges;
                                $soldeJoursCET+=$mouvementCET;
                                break;
                            case ICet::CET_RAFP :
                                $nbJoursRAFP += $jours;
                                $mouvementCET = 0-$nbJoursRAFP;
                                $soldeJoursCET+=$mouvementCET;
                                break;
                            case ICet::CET_PAYER :
                                $nbJoursIndemnises += $jours;
                                $mouvementCET = $jours - $nbJoursIndemnises - $nbJoursConges - $nbJoursRAFP;
                                $soldeJoursCET+=$mouvementCET;
                                break;
                        }
                    }
        
                    switch($actions[$j]->getTypeAction()->getLibelle()){
                        case ICet::CET_OUVERTURE :
                            //TOUT EST A 0; ON PASSE A LA SUITE
                            break;
                        case ICet::CET_EPARGNER :
                            $mouvementCET = $jours - $nbJoursIndemnises - $nbJoursConges - $nbJoursRAFP;
                            $soldeJoursCET+=$mouvementCET;
                            break;
                        case ICet::CET_CONGES :
                            $nbJoursConges += $jours;
                            $mouvementCET = 0-$nbJoursConges;
                            $soldeJoursCET+=$mouvementCET;
                            break;
                        case ICet::CET_RAFP :
                            $nbJoursRAFP += $jours;
                            $mouvementCET = 0-$nbJoursRAFP;
                            $soldeJoursCET+=$mouvementCET;
                            break;
                        case ICet::CET_PAYER :
                            $nbJoursIndemnises += $jours;
                            $mouvementCET = $jours - $nbJoursIndemnises - $nbJoursConges - $nbJoursRAFP;
                            $soldeJoursCET+=$mouvementCET;
                            break;
                    }
        
                    if($mouvementCET > 0){
                        $mouvementCET = "+".$mouvementCET;
                    }
                    $ligne[7] = $soldeJoursCET;
                    $ligne[8] = $jours;
                    $ligne[9] = $nbJoursIndemnises;
                    $ligne[10] = $nbJoursConges;
                    $ligne[11] = $nbJoursRAFP;
                    $ligne[12] = $mouvementCET;
                }
            }else{
                $ligne[7] = 0;
                $ligne[8] = 0;
                $ligne[9] = 0;
                $ligne[10] = 0;
                $ligne[11] = 0;
                $ligne[12] = 0;
            }
            array_push($bilan, $ligne);
        }
        array_unshift($bilan, $champsAffiches);
        return $bilan;
    }

    /**
     * Permet de convertir le tableau retourné par PersonnelRepository->recherchePersonnel()
     * ou PersonnelRepository->rechercheAvancePersonnel() en tableau d'objets
     *
     * @param array $personnelArray
     * @return void
     */
    public function arrayToObject(array $personnelArray){
        $personnels = array();
        //ON ENLEVE LES DOUBLONS RETOURNÉS PAR MYSQL
        for($i=0; $i<sizeof($personnelArray);$i++){ 
            if($i>0 && $personnelArray[$i]["numen"] == $personnelArray[$i-1]["numen"]){
                continue;
            }else{
                $personnel = $this->findOneByNumen($personnelArray[$i]["numen"]);
                $personnel->setSource('BDD');
                array_push($personnels, $personnel);
            }
        }
        return $personnels;
    }


    // /**
    //  * @return Personnel[] Returns an array of Personnel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Personnel
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
