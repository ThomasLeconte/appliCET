<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Form\GradeType;
use App\Repository\GradeRepository;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb

/**
 * @Route("/grade")
 * @Security("is_granted('ROLE_GRADE')")
 */
class GradeController extends BaseController
{
    /**
     * @Route("/", name="grade_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(GradeRepository $gradeRepository): Response
    {
        return $this->render('grade/index.html.twig', [
            'grades' => $gradeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="grade_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $grade = new Grade();
        $form = $this->createForm(GradeType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($grade);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('grade_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('grade/new.html.twig', [
            'grade' => $grade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="grade_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(Grade $grade, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('grade/show.html.twig', [
            'grade' => $grade,
        ]);
    }

    /**
     * @Route("/{code}/edit", name="grade_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, Grade $grade, $code): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(GradeType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('grade_index', [
                'code' => $grade->getCode(),
            ]);
        }

        return $this->render('grade/edit.html.twig', [
            'grade' => $grade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="grade_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, Grade $grade): Response
    {
    	$this->checkCSRF($request);
        $entityManager = $this->getDoctrine()->getManager();
        
        try{
            $entityManager->remove($grade);
            $entityManager->flush();
            $this->addFlashSucces("Le paramètre a bien été supprimé");
            return $this->redirectToRoute('grade_index');
        }catch(ForeignKeyConstraintViolationException $e){
            return $this->render('exception/index.html.twig', [
                'erreur' => "Erreur : Le grade que vous tentez de supprimer est affecté à un personnel. Il est donc ".
                            "impossible de le supprimer.",
                'infosErreur' => $e->getMessage(),
                'intitule' => "Suppresion d'un grade de personnel"
            ]);
        }
    }
}
