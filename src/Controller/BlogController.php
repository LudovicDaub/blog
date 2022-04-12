<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        //récupère les articles -> depuis l'entity et le repository
        $articles = $repo->findAll();

        //affichage de la page
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="app_home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="app_blog_create")
     * @Route("/blog/{id}/edit", name="app_blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$article) {
            $article = new Article;
        }
        //Appel du formulaire depuis Form
        $form = $this->createForm(ArticleType::class, $article);
        //envoie l'affichage du form
        $form->handleRequest($request);
        //controle si le formualire est bien rempli
        if ($form->isSubmitted() && $form->isValid()) {
            //vérifie si l'article à une id
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('app_blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="app_blog_show")
     */
    public function show(Article $article): Response
    {
        return $this->render('blog/show.html.twig', ['article' => $article]);
    }
}
