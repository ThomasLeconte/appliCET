<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Action;
use App\Form\ActionType;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Personnel;
use App\Form\ActionParPersonnelType;
use App\Repository\ActionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb

/**
 * @Route("/action")
 * @Security("is_granted('ROLE_ACTION')")
 */
class ActionController extends BaseController
{
    /**
     * @Route("/", name="action_index", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function index(ActionRepository $actionRepository): Response
    {
        return $this->render('action/index.html.twig', [
            'actions' => $actionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="action_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function new(Request $request): Response
    {
        $action = new Action();
        $form = $this->createForm(ActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($action);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('action_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('action/new.html.twig', [
            'action' => $action,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="action_show", methods={"GET"})
     * @Security("is_granted('ROLE_LECTURE')")
     */
    public function show(Action $action, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('action/show.html.twig', [
            'action' => $action,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="action_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ECRITURE')")
     */
    public function edit(Request $request, Action $action, $id): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(ActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('action_index', [
                'id' => $action->getId(),
            ]);
        }

        return $this->render('action/edit.html.twig', [
            'action' => $action,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="action_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_SUPPRESSION')")
    */
    public function delete(Request $request, Action $action): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($action);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('action_index');
    }

    /**
     * @Route("/actions/{numen}", name="action_listerParPersonnel", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_LECTURE')")
     * 
     * Permet de lister les actions d'une personne ainsi que de pouvoir en ajouter une nouvelle
     * si son compte CET n'est pas clôturé.
     */
    public function listerActions(string $numen, Request $request, ActionRepository $actionRepository): Response
    {
        $personnel = $this->getDoctrine()
        ->getRepository(Personnel::class)
        ->findOneByNumen($numen);

        $action = new Action();

        $form = $this->createForm(ActionParPersonnelType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $action->setPersonnel($personnel);
                $entityManager->persist($action);
                $entityManager->flush();
    
                $this->addFlashSucces("L'item a bien été ajouté");
                return $this->render('action/actionsParPersonnel.html.twig', [
                    'actions' => $actionRepository->findByPersonnel($personnel),
                    'personnel' => $personnel,
                    'form' => $form->createView()
                ]);
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('action/actionsParPersonnel.html.twig', [
            'actions' => $actionRepository->findByPersonnel($personnel),
            'personnel' => $personnel,
            'form' => $form->createView()
        ]);
    }
}
