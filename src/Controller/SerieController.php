<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Serie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SerieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;



class SerieController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {

        $serieRepository = $this->getDoctrine()
            ->getRepository(Serie::class)
            ->findAll();

        $categorie = new Categorie();

        $categorieRepository = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categorie = $form->getData();
            $entityManager->persist($categorie);
            $entityManager->flush();
        }

        return $this->render('serie/index.html.twig', [
            'series' => $serieRepository,
            'categories' => $categorieRepository,
            'formCategories' => $form->createView()
        ]);

    }


    /**
     * @Route("/series", name="series")
     */
    public function series( Request $request, EntityManagerInterface $entityManager){

        $serie = new Serie();

        $serieRepository = $this->getDoctrine()
            ->getRepository(Serie::class)
            ->findAll();

        $categorie = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();

        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $serie = $form->getData();

            $categories = $this->getDoctrine()
                ->getRepository(Categorie::class)
                ->find($request->request->get('Categorie'));
            $serie->setCategorieId($categories);

            $entityManager->persist($serie);
            $entityManager->flush();

            $this->redirectToRoute('series');
        }
        return $this->render('serie/series.html.twig', [
            'series' => $serieRepository,
            'formSerie' => $form->createView(),
            'categories' => $categorie

        ]);
    }


    /**
     * @Route("/singleSerie/{{id}}", name="singleSerie")
     */
    public function singleSerie ( $id, Request $request, EntityManagerInterface $entityManager){


        $serie = $this->getDoctrine()
            ->getRepository(Serie::class)
            ->find($id);


        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $serie = $form->getData();


            $entityManager->persist($serie);
            $entityManager->flush();


        }
        return $this->render('serie/singleSerie.html.twig', [
            'series' => $serie,
            'formUpdate' => $form->createView()

        ]);
    }

    /**
     * @Route("/serie/remove/{id}", name="remove")
     */
    public function removeSeries($id, EntityManagerInterface $entityManager){
        $serie = $this->getDoctrine()->getRepository(Serie::class)->find($id);

        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('series');
    }

    /**
     * @Route("/categories/{{id}}", name="categories")
     */
    public function categories ($id, Request $request, EntityManagerInterface $entityManager){

        $categorieRepository = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->find($id);

        $form = $this->createForm(CategorieType::class, $categorieRepository);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $categorieRepository = $form->getData();

            $entityManager->persist($categorieRepository);
            $entityManager->flush();

        }

        $entityManager->persist($categorieRepository);
        $entityManager->flush();

        return $this->render('serie/categories.html.twig', [
            'categories' => $categorieRepository,
            'formCategories' => $form->createView()
        ]);
    }

    /**
     * @Route("/removeCategories/{id}", name="removeCategorie")
     */
    public function removeCategorie($id, EntityManagerInterface $entityManager){
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('serie');
    }

}