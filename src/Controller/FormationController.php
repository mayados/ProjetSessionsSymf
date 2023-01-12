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

    #[Route('/formation/add', name: 'add_formation')]
    public function add(ManagerRegistry $doctrine, Formation $formation = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('formation/add.html.twig', [
            'formAddFormation' => $form->createView() 
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

}
