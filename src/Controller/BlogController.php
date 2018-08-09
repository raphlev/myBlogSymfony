<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Article;
use App\Repository\ArticleRepository;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();


        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/",name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenu ici les amis !",
            'age' => 31
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */

    //public function show(ArticleRepository $repo, $id){
    public function show(Article $article){
        //$repo=$this->getDoctrine()->getRepository(Article::class);

        //$article = $repo->find($id);

        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }
}
