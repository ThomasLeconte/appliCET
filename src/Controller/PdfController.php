<?php

namespace App\Controller;

use App\Tool\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\Action;
use App\Controller\TCPDF;
use App\Entity\Personnel;
use App\Tool\Interfaces\ICet;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_PDF')")
 */
class PdfController extends AbstractController
{
    /**
     * @Route("/pdf/{idLdap}", name="pdf")
     * @Security("is_granted('ROLE_LECTURE')")
     * 
     * Fonction permettant de générer le PDF listant l'historique CET d'une personne
     * (ne renvoie pas de vue, juste une pop-up de téléchargement du PDF)
     */
    public function index(string $idLdap)
    {
        
        $personnel = $this->getDoctrine()
        ->getRepository(Personnel::class)
        ->findOneByIdLdap($idLdap);

        $actions = $this->getDoctrine()
        ->getRepository(Action::class)
        ->findByPersonnelOrderByDate($personnel->getNumen());

        /*===========================================*/
        /*==================TCPDF====================*/
        /*===========================================*/

        $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        if(null==$personnel->getGrade()){
            $grade = "Aucun";
        }else{
            $grade = $personnel->getGrade()->getLibelleLong();
        }
        
        if(null==($personnel->getAffectation())){
            $affectation = "Aucune";
        }else{
            $affectation = $personnel->getAffectation()->getSigle()." ".$personnel->getAffectation()->getLibelle()."(".$personnel->getAffectation()->getRne().")";
        }
        $pdf->setInformations($personnel->getNom(), $personnel->getPrenom(), $affectation, $grade);

        $pdf->SetTopMargin(45);

        $pdf->AddPage();
        
        $pdf->Cell(190,10,"A alimenté son compte épargne temps comme suit : ",0,1,'L');

        //CONSTRUCTION DU TABLEAU
        $html = "
            <style>
                table{
                    border-collapse:collapse;
                }
                tr td{
                    line-height:25px;
                }
                td, th{
                    border: 1px solid black;
                    text-align:center;
                }
                table tr th{
                    font-size:10px;
                    font-weight:bold;
                    background-color:#dcdde1;
                    line-height:15px;
                }

                #signatures{
                    display:inline-block;
                }

                #signatures span{
                    margin-left : 4em;
                }
            </style>

            <table>
                <thead>
                    <tr>
                        <th>Année réf.</th>
                        <th>Nb de congés pris au 31/12 de l'année</th>
                        <th>Nb de jours versés au CET au 31/12 de l'année</th>
                        <th>Nb de jours CET indemnisés au titre de l'année</th>
                        <th>Nb de jours CET consommés en cng au titre de l'année</th>
                        <th>Nb de jours CET consommés en RAFP au titre de l'année</th>
                        <th>Mouvements sur le CET au 31/12 de l'année</th>
                        <th>Solde de jours sur le CET au 31/12 de l'année</th>
                    </tr>
                </thead>
                <tbody>
        ";

        $soldeJoursCET = 0;
        $actionSuivante = 0;
        for($i=0;$i<sizeof($actions);$i++){
            $annee = $actions[$i]->getAnnee();
            $jours = strval($actions[$i]->getJours());
            $conges = strval($actions[$i]->getConges());
            $nbJoursIndemnises = 0;
            $nbJoursConges = 0;
            $nbJoursRAFP = 0;
            $mouvementCET = 0;

            //CONTROLE SI L'ACTION SUIVANTE EST DE LA MÊME ANNÉE QUE CETTE ACTION
            if($i+1<count($personnel->getActions()) && $actions[$i+1]->getAnnee() == $actions[$i]->getAnnee()){
                $actionSuivante = $actions[$i+1]->getId();
                switch($actions[$i+1]->getTypeAction()->getLibelle()){
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

            if($actionSuivante == $actions[$i]->getId()){
                $actionSuivante = 0;
                continue;
            }

            switch($actions[$i]->getTypeAction()->getLibelle()){
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

            $html=$html."
                <tr>
                    <td>".$annee."</td>
                    <td>".$conges."</td>
                    <td>".$jours."</td>
                    <td>".$nbJoursIndemnises."</td>
                    <td>".$nbJoursConges."</td>
                    <td>".$nbJoursRAFP."</td>
                    <td>".$mouvementCET."</td>
                    <td>".$soldeJoursCET."</td>                
                </tr>
            ";

        }

        $html = $html.'
            </tbody>
        </table>
        <p><strong>Solde du compte épargne temps au '.date("d/m/Y").' : '.$soldeJoursCET.' jour(s)</strong></p>
        <br><br>
        ';
        
        $pdf->WriteHTMLCell(192,0,9,'',$html,0);
        $pdf->Ln(15);
        /*$pdf->Cell(50,10,"Qualité du signataire",0,0,'C');
        $pdf->Cell(50,10,"Signature",0,0,'C');*/
        // ---------------------------------------------------------
        // NOM DU FICHIER
        $pdf->Output('CET-'.$personnel->getNom()."-".$personnel->getPrenom()."_".date("d")."-".date("m")."-".date("Y")."-".'.pdf', 'I');
        

        //============================================================+
        // END OF FILE
        //============================================================+
    }
}
