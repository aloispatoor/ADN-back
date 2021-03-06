<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

        $articles = $paginator->paginate($articles, $request->query->getInt('page', 1), 6);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/{id}<\d+>}', name: 'app_article_single')]
    public function single(Article $article, CommentRepository $commentRepository, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        
        // Section Comments
        $user = $this->getUser();
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);
        $comments = $paginator->paginate($comments, $request->query->getInt('page', 1), 6);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();
            
            return $this->redirectToRoute('app_article_single', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/single.html.twig', [
            'article' => $article,
            'user' => $user,
            'comments' => $comments,
            'commentform' => $form->createView()
        ]);
    }

    #[Route('/create', name: 'app_article_create')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez ??tre connect?? en tant que membre de l\'association pour acc??der ?? cette page')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $article->setAuthor($this->getUser());
            $em->persist($article);
            $em->flush();
            
            return $this->redirectToRoute('app_article_index');
        }
        
        return $this->renderForm('article/create.html.twig', [
            'form' => $form, 
            'action' => 'Create'
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_article_edit')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez ??tre connect?? en tant que membre de l\'association pour acc??der ?? cette page')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $article->getAuthor()) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash('editArticle', 'Article modifi?? avec succ??s !');

                return $this->redirectToRoute('app_article_index');
            }

            return $this->renderForm('article/create.html.twig', [
                'form' => $form,
                'article' => $article,
                'action' => 'Edit'
                ]);
        }

        return $this->redirectToRoute('app_article_index');
    }

    #[Route('/delete/{id<\d+>}', name: 'app_article_delete')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez ??tre connect?? en tant que membre de l\'association pour acc??der ?? cette page')]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $article->getAuthor()) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('deleteArticle', 'L\'article a ??t?? supprim?? avec succ??s');

            return $this->redirectToRoute('app_article_index');
        }
        return $this->redirectToRoute('app_article_index');
    }
}

