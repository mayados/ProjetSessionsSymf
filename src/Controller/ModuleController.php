<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
                // $module->setCategorie();
                $module = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($module);
                $entityManager->flush();

                $this->addFlash('success', 'Module créé avec succès');

                return $this->redirectToRoute('app_session');
            }

            return $this->render('module/add.html.twig', [
                'formAddModule' => $form->createView() 
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }


    
    #[Route('/module/delete/{id}/{idCategorie}', name: 'delete_module')]
    #[ParamConverter("module", options: ["mapping" => ["id" => "id"]])]
    #[ParamConverter("categorie", options: ["mapping" => ["idCategorie" => "id"]])]
    public function deleteModule(ManagerRegistry $doctrine, Module $module, ModuleRepository $mr, int $idCategorie): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
           
            /* Ici, on fait appel à deux fonctions présentes de base dans le ProgrammeRepository : find() et remove() */
            $entityManager = $doctrine->getManager();
            $moduleASupprimer = $entityManager->getRepository(Module::class)->find($module->getId());
            $entityManager->remove($moduleASupprimer);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();

            $this->addFlash('success', 'Module supprimé');

            return $this->redirectToRoute('show_categorie',
            ['id' => $idCategorie]);            

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

}
