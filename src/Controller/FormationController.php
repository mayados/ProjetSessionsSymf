<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use Doctrine\ORM\EntityManager;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $fr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $listeFormations = $fr->findAll();

        return $this->render('formation/index.html.twig', [
            'listeFormations' => $listeFormations,
        ]);
    }

    #[Route('/formation/edit/{id}', name: 'edit_formation')]
    #[Route('/formation/add', name: 'add_formation')]
    public function add(ManagerRegistry $doctrine, Formation $formation = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        // Dans le cas où il n'y a pas de formation = qu'il n'y a pas d'id, on $formation est égal à une nouvelles instance de classe de Formation
        if (!$formation) {
            $formation = new Formation();
        }

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/add.html.twig', [
            'formAddFormation' => $form->createView(),
            'edit' => $formation->getId(),
        ]);
    }

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation, SessionRepository $sr, FormationRepository $fr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $formation = $fr->find($formation->getId());
        $pastSessions = $sr->findPastSessionsByFormation($formation->getId());
        $futureSessions = $sr->findFutureSessionsByFormation($formation->getId());
        $progressSessions = $sr->findProgressSessionsByFormation($formation->getId());        

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'pastSessions' => $pastSessions,
            'futureSessions' => $futureSessions,
            'progressSessions' => $progressSessions,
        ]);
    }


    #[Route('/removeFormation/{id}', name: 'remove_formation')]
    public function removeFormation(ManagerRegistry $doctrine, FormationRepository $fr, Formation $formation): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }


        $entityManager = $doctrine->getManager();

        $formationASupprimer = $fr->find($formation->getId());

        $fr->remove($formationASupprimer);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Redirige vers Home
        return $this->redirectToRoute(
            'app_formation',
        );

    }    

}
