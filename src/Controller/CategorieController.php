<?php

namespace App\Controller;

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
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

        $listeCategories = $cr->findAll();

        return $this->render('categorie/index.html.twig', [
            'listeCategories' => $listeCategories,
        ]);
    }

    #[Route('/categorie/edit/{id}', name: 'edit_categorie')]
    #[Route('/categorie/add', name: 'add_categorie')]
    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }

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

            // $this->addFlash('notice',
            // 'La catégorie a été ajoutée');

            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/add.html.twig', [
            'formAddCategorie' => $form->createView(),
            'edit' => $categorie->getId(),
        ]);
    }

    #[Route('/removeCategorie/{id}', name: 'remove_categorie')]
    public function removeStagiaire(ManagerRegistry $doctrine, CategorieRepository $cr, Categorie $categorie): Response
    {

        //On vérifie s'il y a un user (comme ça pas de modif possible autrement)
        // if($this->getUser()) {
            
        // } else {
        //     return $this->redirectToRoute("app_login");
        // }


        $entityManager = $doctrine->getManager();

        $categorieASupprimer = $cr->find($categorie->getId());

        $cr->remove($categorieASupprimer);
        /* flush() sauvegarde les changements effectués en base de données */
        $entityManager->flush();


        //Redirige vers Home
        return $this->redirectToRoute(
            'app_categorie',
        );

    }    


}
