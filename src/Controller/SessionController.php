<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\SessionStagiaireType;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
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

    //formulaire d'édition
    #[Route('/session/edit/{id}', name: 'edit_session')]
    //formulaire ajout session
    #[Route('/session/add', name: 'add_session')]
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
    {

        //Si la session n'existe pas(= s'il n'y a pas d'id) on passe par add_session = form de création
        // Si ca existe ça passe par les datas edit (voir plus bas)
        if(!$session) {
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $programme = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/add.html.twig', [
            'formAddSession' => $form->createView(),
            'edit' => $session->getId(),
        ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr): Response
    {
        //Indiquer le chemin vers la méthode pour display les stagiaires non-inscrits
        $stagiairesNonInscrits = $sr->findNonInscrits($session->getId());
        $modulesNonProgrammes = $sr->findModulesNonProgrammes($session->getId());

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'stagiairesNonInscrits' => $stagiairesNonInscrits,
            'modulesNonProgrammes' => $modulesNonProgrammes,
        ]);
    }

    #[Route('/session/addStagiaire/{id}/{idStagiaire}', name: 'add_stagiaireSession')]
    //Pour savoir de quel élément il s'agit dans la route on utilise le paramConverter quand il y a plusieurs éléments à passer
    //  options : 1er paramètre -> le nom de la variable passée / 2-> le nom de la propriété correspondante dans l'Entité concernée
    #[ParamConverter("session", options:["mapping" => ["id" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idStagiaire" => "id"]])]
    public function addtagiaire(ManagerRegistry $doctrine , Session $session, Stagiaire $stagiaire): Response
    {
        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            // Si une session est complète, on redirige à la même page (=pas d'action possible même direct dans l'url)
            if(count($session->getStagiaires()) >= $session->getNbPlaces()) {
                return $this->redirectToRoute("show_session", ["id" => $session->getId()]);
            } else {
                /* Des choses vont être changées en base de données session_stagiaire, il faut donc doctrine */
                $entityManager = $doctrine->getManager();
                /* On cherche de quelle instance de class il s'agit grâce à l'id du stagiaire envoyée en url */      
                /* On appelle une méthode de la Class, pas une méthode du repository */
                /* On dit que le stagiaire concerné est celui que la méthode find() renvoie  */
                $session->addStagiaire($stagiaire);
                /* flush() sauvegarde les changements effectués en base de données */
                $entityManager->flush();
        
        
                //Redirige sur la session sur laquelle on se trouvait
                //On a déjà l'objet session grâce à l'id envoyer dans le path + l'objet session déclaré en paramètre (= session précise)
                return $this->redirectToRoute('show_session',
                    ['id' => $session->getId()]);
            }
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }
        
    }    

    #[Route('/session/removeStagiaire/{id}/{idStagiaire}', name: 'remove_stagiaire')]
    #[ParamConverter("session", options:["mapping" => ["id" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idStagiaire" => "id"]])]
    public function removeStagiaire(ManagerRegistry $doctrine , Session $session, Stagiaire $stagiaire): Response
    {

        /* Des choses vont être changées en base de données session_stagiaire, il faut donc doctrine */
        $entityManager = $doctrine->getManager();
        /* On cherche de quelle instance de class il s'agit grâce à l'id du stagiaire envoyée en url */       
        /* On appelle une méthode de la Class, pas une méthode du repository */
        /* On dit que le stagiaire concerné est celui que la méthode find() renvoie  */
        $session->removeStagiaire($stagiaire);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Redirige sur la session sur laquelle on se trouvait
        //On a déjà l'objet session grâce à l'id envoyer dans le path + l'objet session déclaré en paramètre (= session précise)
        return $this->redirectToRoute('show_session',
    ['id' => $session->getId()]);

    }








}
