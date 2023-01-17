<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $fr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $listeFormateurs = $fr->findAll();

        return $this->render('formateur/index.html.twig', [
            'listeFormateurs' => $listeFormateurs,
        ]);
    }

    #[Route('/removeFormateur/{id}', name: 'remove_formateur')]
    public function removeStagiaire(ManagerRegistry $doctrine, FormateurRepository $fr, Formateur $formateur): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }


        $entityManager = $doctrine->getManager();

        $formateurASupprimer = $fr->find($formateur->getId());

        $fr->remove($formateurASupprimer);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Redirige vers Home
        return $this->redirectToRoute(
            'app_formateur',
        );

    }    

    
    #[Route('/formateur/add', name: 'add_formateur')]
    public function add(ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $form = $this->createForm(FormateurType::class, $formateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formateur = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_formateur');
        }

        return $this->render('formateur/add.html.twig', [
            'formAddFormateur' => $form->createView() 
        ]);
    }

}
