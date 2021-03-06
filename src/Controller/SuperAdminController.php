<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SuperAdminController extends AbstractController
{
    #[Route('/superadmin', name: 'app_super_admin_articles')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function articles(ArticleRepository $articleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $articles = $articleRepository->findAll();

        return $this->render('super_admin/articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/superadminUsers', name: 'app_super_admin_users')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $users = $userRepository->findAll();

        return $this->render('super_admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/superadminComments', name: 'app_super_admin_comments')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function comments(CommentRepository $commentRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $comments = $commentRepository->findAll();

        return $this->render('super_admin/comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/superadminUserDelete/{id<\d+>}', name: 'app_super_admin_user_delete')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $user = $user->getId();
        if ($user) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('deleteUser', 'L\'utilisateur-rice a été supprimé-e avec succès');

            return $this->redirectToRoute('app_super_admin_users');
        }
        return $this->redirectToRoute('app_super_admin_users');
    }

    #[Route('/superadminArticleDelete/{id<\d+>}', name: 'app_super_admin_article_delete')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function deleteArticle(Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $article = $article->getId();
        if ($article) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('deleteArticle', 'L\'article a été supprimé avec succès');

            return $this->redirectToRoute('app_super_admin_articles');
        }
        return $this->redirectToRoute('app_super_admin_articles');
    }

    #[Route('/superadminCommentDelete/{id<\d+>}', name: 'app_super_admin_comment_delete')]
    #[IsGranted('ROLE_SUPERADMIN', message: 'Vous devez être administrateur-rice pour accéder à cette page')]
    public function deleteComment(Comment $comment, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $comment = $comment->getId();
        if ($comment) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('deleteComment', 'Le commentaire a été supprimé avec succès');

            return $this->redirectToRoute('app_super_admin_comments');
        }
        return $this->redirectToRoute('app_super_admin_comments');
    }
}
