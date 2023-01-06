<?php

namespace App\Controller;

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

    #[Route('/programme/add', name: 'add_programme')]
    public function add(ManagerRegistry $doctrine, Programme $programme = null, Request $request): Response
    {

        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $programme = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('programme/add.html.twig', [
            'formAddProgramme' => $form->createView() 
        ]);
    }

    #[Route('/programme/delete/{id}', name: 'delete_programme')]
    public function deleteProgramme(ManagerRegistry $doctrine , int $id): Response
    {

        /* Ici, on fait appel à deux fonctions présentes de base dans le ProgrammeRepository : fin() et remove() */
        $entityManager = $doctrine->getManager();
        $programme = $entityManager->getRepository(Programme::class)->find($id);
        $entityManager->remove($programme);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Voir comment rediriger sur une vue précise. Par exemple la session où l'on était
        return $this->redirectToRoute('app_session');

    }



}
