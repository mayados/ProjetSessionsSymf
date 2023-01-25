<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\SessionRepository;
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
        if($this->getUser()) {
            
            $listeFormateurs = $fr->findAll();

            return $this->render('formateur/index.html.twig', [
                'listeFormateurs' => $listeFormateurs,
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/removeFormateur/{id}', name: 'remove_formateur')]
    public function removeFormateur(ManagerRegistry $doctrine, FormateurRepository $fr, Formateur $formateur, Session $session, SessionRepository $sr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $entityManager = $doctrine->getManager();

            $formateurASupprimer = $fr->find($formateur->getId());

            // Je cherche la collection d'objet sessions du formateur trouvé précédement
            $sessions = $formateurASupprimer->getSessions();

            // dd($sessions);



            /* Comme il y a plusieurs éléments nous parcourons chaque élément
            => pour chaque session concernée, on met le référent à null */
            foreach ($sessions as $session) {

                $session->setReferent(Null);

            }

            $fr->remove($formateurASupprimer);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();

            $this->addFlash('success', 'Formateur supprimé');

            //Redirige vers Home
            return $this->redirectToRoute(
                'app_formateur',
            );

        } else {
            return $this->redirectToRoute("app_login");
        }
    }    

    #[Route('/formateur/edit/{id}', name: 'edit_formateur')]
    #[Route('/formateur/add', name: 'add_formateur')]
    public function add(ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            $edit = false;
            if($formateur){
                $edit = true;
            }
            
            //Si la session n'existe pas(= s'il n'y a pas d'id) on passe par add_session = form de création
            // Si ca existe ça passe par les datas edit (voir plus bas)
            if (!$formateur) {
                $formateur = new Formateur();
            }

            $form = $this->createForm(FormateurType::class, $formateur);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $formateur = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($formateur);
                $entityManager->flush();

                ($edit)?$this->addFlash('success', 'Formateur modifié'):$this->addFlash('success', 'Formateur ajouté');

                return $this->redirectToRoute('app_formateur');
            }

            return $this->render('formateur/add.html.twig', [
                'formAddFormateur' => $form->createView(),
                'edit' => $formateur->getId(),
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            return $this->render('formateur/show.html.twig', [
                'formateur' => $formateur,
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }   

}
