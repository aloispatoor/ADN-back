<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\AvatarRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index')]
    public function index(CommentRepository $commentRepository, AvatarRepository $avatarRepository, User $user): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);
        $userAvatar = $avatarRepository->findBy(['user' => $user->getUserIdentifier()]);
        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
            'avatar' => $userAvatar,
        ]);
    }

    #[Route('/create', name: 'app_comment_create')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function create(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('addComment', 'Le commentaire a été ajouté avec succès');
            
            return $this->redirectToRoute('app_article_single');
        }
        
        return $this->renderForm('comment/create.html.twig', [
            'commentform' => $form,
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $comment->getAuthor()) {
                $em->remove($comment);
                $em->flush();
                $this->addFlash('deleteComment', 'Le commentaire a été supprimé avec succès');

                return $this->redirectToRoute('app_article_single', [
                    'id' => $comment->getArticle()
                ]);
        }
        return $this->redirectToRoute('app_article_single');
    }
}
