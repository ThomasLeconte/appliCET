<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Normandie\ViewBundle\Controller\BaseController; //Modification de  stb
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/user")
 */
class UserController extends BaseController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
        	if ($form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
    			$this->addFlashSucces("L'item a bien été ajouté");
                return $this->redirectToRoute('user_index');
            }
            else{
				$this->addFlashErreur("L'item n'a pas pu être créé");
        	}    
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(User $user, Request $request): Response
    {
    	$this->checkCSRF($request);
    	    	    	
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function edit(Request $request, User $user, $id): Response
    {
    	$this->checkCSRF($request);
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $this->getDoctrine()->getManager()->flush();
            
			$this->addFlashSucces("L'item a bien été enregistré");
                                    
            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER')")
    */
    public function delete(Request $request, User $user): Response
    {
    	$this->checkCSRF($request);
    	$entityManager = $this->getDoctrine()->getManager();
    	$entityManager->remove($user);
    	$entityManager->flush();
    	$this->addFlashSucces("Le paramètre a bien été supprimé");
        return $this->redirectToRoute('user_index');
    }
}
