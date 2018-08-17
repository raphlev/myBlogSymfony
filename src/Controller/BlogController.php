<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager) {

        // la var $article peut etre null dans le cas new en creation
        if(!$article) {
            $article = new Article();
        }

        // DEBUG Creation d'un dump pour voir contenu d'une requete variable, ici la requete HTTP
        //dump($request); 

        /*
        // Methode fastidieuse pour crer un formulaire...
        if($request->request->count() > 0) {
            $article = new Article();
            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new \DateTime());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }*/
        // Creer un formulaire avec form builder de synfony - ce formulaire est lié à l'article
       
        // $article = new Article();

        //$article->setTitle("Titre d'exemple")
        //        ->setContent("Le contenu de l'article");

       //$form = $this->createFormBuilder($article)
       //              ->add('title'/*, TextType::class, [
       //                  'attr' => [
       //                      'placeholder' => "Titre de l'article",
                             //'class' => 'form-control'
       //                  ]
       //              ]*/)
       //              ->add('content'/*, TextareaType::class, [
       //                 'attr' => [
       //                     'placeholder' => "Contenu de l'article",
       //                     //'class' => 'form-control'
       //                 ]
       //             ]*/)
       //              ->add('image'/*, TextType::class, [
       //                 'attr' => [
       //                     'placeholder' => "Image de l'article",
       //                     //'class' => 'form-control'
       //                 ]
       //             ]*/)
                    // button - c'est mieux de le mettre dans le twig
                    // ->add('save',SubmitType::class,[
                    //     'label' => 'Enregistrer'
                    // ])
        //             ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        // DEBUG - Creation d'un dump pour voir contenu d'une requete variable, ici l'article
        //dump($article); 

        // Declaration d'une variable formArticle envoyé a twig grace à un tableau d'attribut, contenant le formulaire. On declare aussi dans ce tableau un boolean indiquant a twig si edit mode ou pas
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    //public function show(ArticleRepository $repo, $id){
    public function show(Article $article, Request $request, ObjectManager $manager){
        //$repo=$this->getDoctrine()->getRepository(Article::class);

        //$article = $repo->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }
        
        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

}
