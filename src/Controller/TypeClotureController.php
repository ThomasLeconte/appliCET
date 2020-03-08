<?php

namespace App\Controller;

use App\Entity\TypeCloture;
use App\Form\TypeClotureType;
use App\Repository\TypeClotureRepository;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/type/cloture")
 * @Security("is_granted('ROLE_TYPE_CLOTURE')")
 */
class TypeClotureController extends BaseController
{
    /**
     * @Route("/", name="type_cloture_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(TypeClotureRepository $typeClotureRepository): Response
    {
        return $this->render('type_cloture/index.html.twig', [
            'type_clotures' => $typeClotureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="type_cloture_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $typeCloture = new TypeCloture();
        $form = $this->createForm(TypeClotureType::class, $typeCloture);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($typeCloture);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('type_cloture_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('type_cloture/new.html.twig', [
            'type_cloture' => $typeCloture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_cloture_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(TypeCloture $typeCloture, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('type_cloture/show.html.twig', [
            'type_cloture' => $typeCloture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_cloture_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, TypeCloture $typeCloture, $id): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(TypeClotureType::class, $typeCloture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('type_cloture_index', [
                'id' => $typeCloture->getId(),
            ]);
        }

        return $this->render('type_cloture/edit.html.twig', [
            'type_cloture' => $typeCloture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_cloture_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, TypeCloture $typeCloture): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($typeCloture);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('type_cloture_index');
    }
}
