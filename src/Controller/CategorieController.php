<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $cr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $listeCategories = $cr->findAll();

            return $this->render('categorie/index.html.twig', [
                'listeCategories' => $listeCategories,
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/categorie/show/{id}', name: 'show_categorie')]
    public function show(ManagerRegistry $doctrine, Module $module = null, Request $request, Categorie $categorie, CategorieRepository $cr): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
           $categorie = $cr->find($categorie->getId());

            /* On veut créer un nouveau module, on va donc créer une nouvelle instance de classe de Module */
            $module = new Module();

            $module->setCategorie($categorie);             

           $form = $this->createForm(ModuleType::class, $module);
           $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()) {
            
               $module = $form->getData();
               $entityManager = $doctrine->getManager();
               $entityManager->persist($module);
               $entityManager->flush();

               $this->addFlash('success', 'Module créé avec succès');

               return $this->redirectToRoute('show_categorie', ['id' => $categorie->getId()]);
           }

            return $this->render('categorie/show.html.twig', [
                'categorie' => $categorie,
                'formAddModule' => $form->createView()

            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    

    #[Route('/categorie/edit/{id}', name: 'edit_categorie')]
    #[Route('/categorie/add', name: 'add_categorie')]
    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {

            $edit = false;
            if($categorie){
                $edit = true;
            }
            
            if (!$categorie) {
                $categorie = new Categorie();
            }

            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $categorie = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($categorie);
                $entityManager->flush();

                ($edit)?$this->addFlash('success', 'Catégorie modifiée'):$this->addFlash('success', 'Catégorie ajoutée');

                return $this->redirectToRoute('app_categorie');
            }

            return $this->render('categorie/add.html.twig', [
                'formAddCategorie' => $form->createView(),
                'edit' => $categorie->getId(),
            ]);

        } else {
            return $this->redirectToRoute("app_login");
        }
    }

    #[Route('/removeCategorie/{id}', name: 'remove_categorie')]
    public function removeStagiaire(ManagerRegistry $doctrine, CategorieRepository $cr, Categorie $categorie): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        if($this->getUser()) {
            
            $entityManager = $doctrine->getManager();

            $categorieASupprimer = $cr->find($categorie->getId());

            $cr->remove($categorieASupprimer);
            /* flush() sauvegarde les changements effectués en base de données */
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie supprimée');

            //Redirige vers Home
            return $this->redirectToRoute(
                'app_categorie',
            );

        } else {
            return $this->redirectToRoute("app_login");
        }
    }    


}
