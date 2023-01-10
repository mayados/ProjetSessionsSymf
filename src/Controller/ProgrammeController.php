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
        return $this->render('programme/index.html.twig', [
            'controller_name' => 'ProgrammeController',
        ]);
    }

    #[Route('/programme/add/{idSession}', name: 'add_programme')]
    public function add(ManagerRegistry $doctrine, Programme $programme = null, Request $request, int $idSession): Response
    {

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

        return $this->render('programme/add.html.twig', [
            'formAddProgramme' => $form->createView() 
        ]);
    }

    #[Route('/programme/delete/{id}/{idSession}', name: 'delete_programme')]
    public function deleteProgramme(ManagerRegistry $doctrine , int $id, int $idSession): Response
    {

        /* Ici, on fait appel à deux fonctions présentes de base dans le ProgrammeRepository : find() et remove() */
        $entityManager = $doctrine->getManager();
        $programme = $entityManager->getRepository(Programme::class)->find($id);
        $entityManager->remove($programme);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();

        return $this->redirectToRoute('show_session',
    ['id' => $idSession]);

    }



}
