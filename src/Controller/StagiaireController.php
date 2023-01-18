<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireFormType;
use App\Repository\StagiaireRepository;
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
        if($this->getUser()) {
        
            $stagiaires = $doctrine->getRepository(Stagiaire::class)->findAll();

            return $this->render('stagiaire/index.html.twig', [
                'stagiaires' => $stagiaires,
            ]);            

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/stagiaires/add', name: 'add_stagiaire')]
    #[Route('/stagiaires/edit/{id}', name: 'edit_stagiaire')]
    public function add(ManagerRegistry $doctrine, Stagiaire $stagiaire = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
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

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/removeStagiaire/{id}', name: 'remove_stagiaire')]
    public function removeStagiaire(ManagerRegistry $doctrine, StagiaireRepository $sr, Stagiaire $stagiaire): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $entityManager = $doctrine->getManager();

            $stagiaireASupprimer = $sr->find($stagiaire->getId());

            $sr->remove($stagiaireASupprimer);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();


            //Redirige vers Home
            return $this->redirectToRoute(
                'app_stagiaire',
            );


        } else {
            return $this->redirectToRoute("app_login");
        }

    }    


    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            return $this->render('stagiaire/show.html.twig', [
                'stagiaire' => $stagiaire,
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }    

}
