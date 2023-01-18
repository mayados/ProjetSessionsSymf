<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\ProgrammeType;
use App\Form\SessionStagiaireType;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class SessionController extends AbstractController
{
    #[Route('/home', name: 'app_session')]
    public function index(SessionRepository $sr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            $pastSessions = $sr->findPastSessions();
            $futureSessions = $sr->findFutureSessions();
            $progressSessions = $sr->findProgressSessions();

            return $this->render('session/index.html.twig', [
                'pastSessions' => $pastSessions,
                'futureSessions' => $futureSessions,
                'progressSessions' => $progressSessions,
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }


    }

    //formulaire d'édition
    #[Route('/session/edit/{id}', name: 'edit_session')]
    //formulaire ajout session
    #[Route('/session/add', name: 'add_session')]
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            //Si la session n'existe pas(= s'il n'y a pas d'id) on passe par add_session = form de création
            // Si ca existe ça passe par les datas edit (voir plus bas)
            if (!$session) {
                $session = new Session();
            }

            $form = $this->createForm(SessionType::class, $session);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
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

        } else {
            return $this->redirectToRoute("app_login");
        }


    }

    // #[Route('/session/add/{id}', name: 'add_programme')]
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr, ManagerRegistry $doctrine, Programme $programme = null, Request $request, int $id): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            //Indiquer le chemin vers la méthode pour display les stagiaires non-inscrits
            $stagiairesNonInscrits = $sr->findNonInscrits($session->getId());
            $modulesNonProgrammes = $sr->findModulesNonProgrammes($session->getId());
            $form = [];
            /* On fait le tour des modules non programmés, et, pour chaque module on crée un 
            nouveau Programme, on lui attribue un module (qui est le module courant comme on 
            est dans un foreach
            On lui attribue une session (la session courant comme on est dans le détail d'une
            session))
            Ensuite, on attribue à $index la création de formulaire avec les éléments de 
            $programme (module et session set auparavant) 
            On dit que $form est un tableau dans lequel on ajoute à chaque tour de boucle 
            la création d'une vue pour le formulaire*/
            foreach ($modulesNonProgrammes as $index => $module) {
                // dd($module->getIntitule());
                $programme = new Programme();
                //On utilise le setter de l'entité Programme pour set le module courant et la session courante
                $programme->setModule($module);
                // dd($module->getIntitule());
                $programme->setSession($session);
                $index = $this->createForm(ProgrammeType::class, $programme);
                $index->handleRequest($request);
                $form[] = $index->createView();


                if ($index->isSubmitted() && $index->isValid()) {
                    //$index est le formulaire (voir plus haut), on obtient donc les données du form
                    $programme = $index->getData();
                    // dd($programme);
                    // var_dump($form->get('duree')->getData());die;
                    $entityManager = $doctrine->getManager();
                    $session = $entityManager->getRepository(Session::class)->find($id);
                    /* On utilise la méthode créée de base dans Session grâce au ManyToMany */
                    $session->addProgramme($programme);
                    $entityManager->persist($programme);
                    // $entityManager->persist($programme);
                    $entityManager->flush();

                    return $this->redirectToRoute(
                        'show_session',
                        ['id' => $session->getId()]
                    );
                }
            }
            //dd($form);
            // die;


            // $form = $this->createForm(ProgrammeType::class, $programme);
            // $form->handleRequest($request);


            // if ($form->isSubmitted() && $form->isValid()) {
            //     $programme = $form->getData();
            //     // var_dump($form->get('duree')->getData());die;
            //     $entityManager = $doctrine->getManager();
            //     $session = $entityManager->getRepository(Session::class)->find($id);
            //     /* On utilise la méthode créée de base dans Session grâce au ManyToMany */
            //     $session->addProgramme($programme);
            //     $entityManager->persist($programme);
            //     // $entityManager->persist($programme);
            //     $entityManager->flush();

            //     return $this->redirectToRoute(
            //         'show_session',
            //         ['id' => $session->getId()]
            //     );
            // }

            return $this->render('session/show.html.twig', [
                'session' => $session,
                'stagiairesNonInscrits' => $stagiairesNonInscrits,
                'modulesNonProgrammes' => $modulesNonProgrammes,
                //form est un tableau contenant chaque $index = chaque formulaire pour chaque module
                'formInfos' => $form
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }


    }

    #[Route('/session/removeSession/{id}', name: 'remove_session')]
    public function removeSession(ManagerRegistry $doctrine, SessionRepository $sr, Session $session): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            $entityManager = $doctrine->getManager();

            $sessionASupprimer = $sr->find($session->getId());

            $sr->remove($sessionASupprimer);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();


            //Redirige vers Home
            return $this->redirectToRoute(
                'app_session',
            );

        } else {
            return $this->redirectToRoute("app_login");
        }

    }

    
    #[Route('/session/addStagiaire/{id}/{idStagiaire}', name: 'add_stagiaireSession')]
    //Pour savoir de quel élément il s'agit dans la route on utilise le paramConverter quand il y a plusieurs éléments à passer
    //  options : 1er paramètre -> le nom de la variable passée / 2-> le nom de la propriété correspondante dans l'Entité concernée
    #[ParamConverter("session", options: ["mapping" => ["id" => "id"]])]
    #[ParamConverter("stagiaire", options: ["mapping" => ["idStagiaire" => "id"]])]
    public function addtagiaire(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire): Response
    {
        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
        // Si une session est complète, on redirige à la même page (=pas d'action possible même direct dans l'url)
            if (count($session->getStagiaires()) >= $session->getNbPlaces()) {
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
                return $this->redirectToRoute(
                    'show_session',
                    ['id' => $session->getId()]
                );
            }
        } else {
            return $this->redirectToRoute("app_login");
        }

    }

    #[Route('/session/removeStagiaire/{id}/{idStagiaire}', name: 'remove_stagiaireSession')]
    #[ParamConverter("session", options: ["mapping" => ["id" => "id"]])]
    #[ParamConverter("stagiaire", options: ["mapping" => ["idStagiaire" => "id"]])]
    public function removeStagiaire(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

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
            return $this->redirectToRoute(
                'show_session',
                ['id' => $session->getId()]
            );

        } else {
            return $this->redirectToRoute("app_login");
        }
    }
}
