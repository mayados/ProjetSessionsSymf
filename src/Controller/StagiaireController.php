<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{

   #[Route('/stagiaires', name: 'app_stagiaire')]
    public function index(ManagerRegistry $doctrine): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $stagiaires = $doctrine->getRepository(Stagiaire::class)->findAll();

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaires/add', name: 'add_stagiaire')]
    #[Route('/stagiaires/edit/{id}', name: 'edit_stagiaire')]
    public function add(ManagerRegistry $doctrine, Stagiaire $stagiaire = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(StagiaireFormType::class, $stagiaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $stagiaire = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($stagiaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/add.html.twig', [
            'formAddStagiaire' => $form->createView(),
            'edit' => $stagiaire->getId(),
        ]);

    }



    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }    

}
