<?php

namespace App\Controller;

use App\Entity\TypeAction;
use App\Form\TypeActionType;
use App\Repository\TypeActionRepository;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/type/action")
 * @Security("is_granted('ROLE_TYPE_ACTION')")
 */
class TypeActionController extends BaseController
{
    /**
     * @Route("/", name="type_action_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(TypeActionRepository $typeActionRepository): Response
    {
        return $this->render('type_action/index.html.twig', [
            'type_actions' => $typeActionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="type_action_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $typeAction = new TypeAction();
        $form = $this->createForm(TypeActionType::class, $typeAction);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($typeAction);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('type_action_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('type_action/new.html.twig', [
            'type_action' => $typeAction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_action_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(TypeAction $typeAction, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('type_action/show.html.twig', [
            'type_action' => $typeAction,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_action_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, TypeAction $typeAction, $id): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(TypeActionType::class, $typeAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('type_action_index', [
                'id' => $typeAction->getId(),
            ]);
        }

        return $this->render('type_action/edit.html.twig', [
            'type_action' => $typeAction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_action_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, TypeAction $typeAction): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($typeAction);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('type_action_index');
    }
}
