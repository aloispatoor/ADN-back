<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/{id}<\d+>}', name: 'app_article_single')]
    public function single(Article $article, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);


        // if ($article->getAuthor() === $user) {
        //     $user = $userRepository->findAll();
        // }
        return $this->render('article/single.html.twig', [
            'article' => $article,
            'user' => $user,
            'comments' => $comments,
        ]);
    }

    #[Route('/create', name: 'app_article_create')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être connecté en tant que membre de l\'association pour accéder à cette page')]
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
        
        return $this->renderForm('article/create.html.twig', ['form' => $form, 'action' => 'Create']);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_article_edit')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être connecté en tant que membre de l\'association pour accéder à cette page')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $article->getAuthor()) {
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('app_article_index');
            }

            return $this->renderForm('article/create.html.twig', ['form' => $form, 'article' => $article, 'action' => 'Edit']);
        }

        return $this->redirectToRoute('app_article_index');
    }

    #[Route('/delete/{id<\d+>}', name: 'app_article_delete')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être connecté en tant que membre de l\'association pour accéder à cette page')]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $article->getAuthor()) {
                $em->remove($article);
                $em->flush();
                $this->addFlash('deleteArticle', 'L\'article a été supprimé avec succès');

                return $this->redirectToRoute('app_article_index');
        }
        return $this->redirectToRoute('app_article_index');
    }
}

