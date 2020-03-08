<?php
namespace App\Tool;
use tecnickcom\tcpdf\TCPDF;

//require("../vendor/tecnickcom/tcpdf/tcpdf.php");

class PDF extends \TCPDF
{
    private $nom;
    private $prenom;
    private $affectation;
    private $grade;

    private $dernierePage = false;


    /**
     * Fonction permettant de récupérer le nom, prénom, affectation(libelle) et le grade(libelle long)
     * d'un personnel.
     *
     * @param string $nom
     * @param string $prenom
     * @param string $affectation
     * @param string $grade
     * @return void
     */
    function setInformations(string $nom, string $prenom, string $affectation, string $grade){
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->affectation = $affectation;
        $this->grade = $grade;
    }

    //ON SURCHARGE LA METHODE Close() de TCPDF
    //On passe dernierePage à true quand le document est terminé, ce qui permet de determiner la dernière page
    public function Close() {
        $this->dernierePage = true;
        parent::Close();
    }

    /**
     * Permet de personnaliser l'en-tête générée par TCPDF
     *
     * @return void
     */
    function Header()
    {
        if($this->getPage() === 1){
            $titre = "Compte Epargne-temps";

            //Logo Acad�mie
            //$this->Image('./icons/logo_caen.png',176,2,25);
            //$this->Image('./icons/LOGO_CAEN.jpg',176,2,25);
            $this->Image('../src/Tool/imagesPDF/logo_academie.png',2,2,35);
            $this->SetY($this->getY()+1);
            //Arial gras 10
            $this->SetFont('helvetica','B',16);
            //Calcul de la largeur du titre et positionnement
            $w=$this->GetStringWidth($titre)+5;
            $this->SetX((210-$w)/2);
            //Titre centr�
            $this->setCellPadding(7);
            $this->Cell($w,10,$titre,0,0,'C'); // sans cadre autour
            $this->setCellPadding(0);
            //Saut de ligne
            $this->Ln(15);
            $this->SetFont('helvetica','',10);
            $this->Cell(190,10,"Historique de ".$this->nom." ".$this->prenom." : ",0,1,'C');
            $this->Ln(-2);
            $this->Cell(190,10,"Grade : ".$this->grade,0,1,'C');
            $this->Ln(-2);
            $this->Cell(190,10,"Affectation : ".$this->affectation,0,1,'C');
        }else{
            $titre = "Compte Epargne-temps";
            $this->SetFont('helvetica','B',16);
            //Calcul de la largeur du titre et positionnement
            $w=$this->GetStringWidth($titre)+5;
            $this->SetX((210-$w)/2);
            //Titre centr�
            $this->setCellPadding(7);
            $this->Cell($w,10,$titre,0,0,'C'); // sans cadre autour
            $this->setCellPadding(0);
            //Saut de ligne
            $this->Ln(15);
            $this->SetFont('helvetica','',10);
            $this->Cell(190,10,"Historique de ".$this->nom." ".$this->prenom." : ",0,1,'C');
            $this->Ln(-2);
            $this->Cell(190,10,"Grade : ".$this->grade,0,1,'C');
            $this->Ln(-2);
            $this->Cell(190,10,"Affectation : ".$this->affectation,0,1,'C');
        }

    }

    /**
     * Permet de personnaliser le footer généré par TCPDF
     *
     * @return void
     */
    function Footer()
    {
        if ($this->dernierePage) {
            //Positionnement � 1,5 cm du bas
            $this->SetY(-70);
            //Police Arial italique 8

            $this->Cell(100,8,'Qualité du signataire',0,0,'C');
            $this->Cell(50,8,'Signature',0,1,'C');
            $this->Image('../src/Tool/imagesPDF/signature.png',120,$this->getY(),80);
            $this->SetY(-15);
            $this->SetFont('helvetica','I',8);
            //Num�ro de page
            $dvisu = date("d/m/Y");
            $this->Cell(85,8,'Généré le : '.$dvisu,0,0,'L');
            $this->Cell(0,8,'Page '.$this->getPage().'/'.$this->getAliasNbPages(),0,0,'R');
        } else {
            //Positionnement � 1,5 cm du bas
            $this->SetY(-15);
            //Police Arial italique 8
            $this->SetFont('helvetica','I',8);
            //Num�ro de page
            $dvisu = date("d/m/Y");
            $this->Cell(85,8,'Généré le : '.$dvisu,0,0,'L');
            $this->Cell(0,8,'Page '.$this->getPage().'/'.$this->getAliasNbPages(),0,0,'R'); 
        }
    }
}

?>