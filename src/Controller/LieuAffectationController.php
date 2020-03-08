<?php

namespace App\Controller;

use App\Entity\LieuAffectation;
use App\Form\LieuAffectationType;
use App\Repository\LieuAffectationRepository;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/lieuaffectation")
 * @Security("is_granted('ROLE_AFFECTATION')")
 */
class LieuAffectationController extends BaseController
{
    /**
     * @Route("/", name="lieu_affectation_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(LieuAffectationRepository $lieuAffectationRepository): Response
    {
        return $this->render('lieu_affectation/index.html.twig', [
            'lieu_affectations' => $lieuAffectationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lieu_affectation_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $lieuAffectation = new LieuAffectation();
        $form = $this->createForm(LieuAffectationType::class, $lieuAffectation);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($lieuAffectation);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('lieu_affectation_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('lieu_affectation/new.html.twig', [
            'lieu_affectation' => $lieuAffectation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{rne}", name="lieu_affectation_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(LieuAffectation $lieuAffectation, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('lieu_affectation/show.html.twig', [
            'lieu_affectation' => $lieuAffectation,
        ]);
    }

    /**
     * @Route("/{rne}/edit", name="lieu_affectation_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, LieuAffectation $lieuAffectation, $rne): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(LieuAffectationType::class, $lieuAffectation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('lieu_affectation_index', [
                'rne' => $lieuAffectation->getRne(),
            ]);
        }

        return $this->render('lieu_affectation/edit.html.twig', [
            'lieu_affectation' => $lieuAffectation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{rne}", name="lieu_affectation_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, LieuAffectation $lieuAffectation): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($lieuAffectation);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('lieu_affectation_index');
    }
}
