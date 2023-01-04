<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
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

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response
    {

        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }



}
