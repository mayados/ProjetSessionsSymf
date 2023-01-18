<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            return $this->render('module/index.html.twig', [
                'controller_name' => 'ModuleController',
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/module/add', name: 'add_module')]
    public function add(ManagerRegistry $doctrine, Module $module = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $form = $this->createForm(ModuleType::class, $module);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $module = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($module);
                $entityManager->flush();

                return $this->redirectToRoute('app_session');
            }

            return $this->render('module/add.html.twig', [
                'formAddModule' => $form->createView() 
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

}
