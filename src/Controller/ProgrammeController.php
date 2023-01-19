<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Form\ProgrammeType;
use App\Repository\ProgrammeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            return $this->render('programme/index.html.twig', [
                'controller_name' => 'ProgrammeController',
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/programme/add/{idSession}', name: 'add_programme')]
    public function add(ManagerRegistry $doctrine, Programme $programme = null, Request $request, int $idSession): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $form = $this->createForm(ProgrammeType::class, $programme);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $programme = $form->getData();
                $entityManager = $doctrine->getManager();
                $session = $entityManager->getRepository(Session::class)->find($idSession);  
                /* On utilise la méthode créée de base dans Session grâce au ManyToMany */
                $session->addProgramme($programme);
                $entityManager->persist($programme);
                // $entityManager->persist($programme);
                $entityManager->flush();

                return $this->redirectToRoute('show_session',
                ['id' => $session->getId()]);
            }

            return $this->render('session/show.html.twig', [
                'formAddProgramme' => $form->createView() 
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/programme/delete/{id}/{idSession}', name: 'delete_programme')]
    public function deleteProgramme(ManagerRegistry $doctrine , int $id, int $idSession): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
           
            /* Ici, on fait appel à deux fonctions présentes de base dans le ProgrammeRepository : find() et remove() */
            $entityManager = $doctrine->getManager();
            $programme = $entityManager->getRepository(Programme::class)->find($id);
            $entityManager->remove($programme);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();

            $this->addFlash('success', 'Module supprimé');

            return $this->redirectToRoute('show_session',
            ['id' => $idSession]);            

        } else {
            return $this->redirectToRoute("app_login");
        }
    }



}
