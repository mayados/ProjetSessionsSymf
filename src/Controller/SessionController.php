<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/home', name: 'app_session')]
    public function index(SessionRepository $sr): Response
    {
        $pastSessions = $sr->findPastSessions();
        $futureSessions = $sr->findFutureSessions();
        $progressSessions = $sr->findProgressSessions();

        return $this->render('session/index.html.twig', [
            'pastSessions' => $pastSessions,
            'futureSessions' => $futureSessions,
            'progressSessions' => $progressSessions,
        ]);
    }

    #[Route('/session/removeStagiaire/{id}/{idStagiaire}', name: 'remove_stagiaire')]
    public function removeStagiaire(ManagerRegistry $doctrine , Session $session, Stagiaire $stagiaire, int $idStagiaire): Response
    {

        /* Des choses vont être changées en base de données session_stagiaire, il faut donc doctrine */
        $entityManager = $doctrine->getManager();
        /* On cherche de quelle instance de class il s'agit grâce à l'id du stagiaire envoyée en url */
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($idStagiaire);        
        /* On appelle une méthode de la Class, pas une méthode du repository */
        /* On dit que le stagiaire concerné est celui que la méthode find() renvoie  */
        $session->removeStagiaire($stagiaire);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Voir comment rediriger sur une vue précise. Par exemple la session où l'on était
        return $this->redirectToRoute('app_session');

    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response
    {

        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }



}
