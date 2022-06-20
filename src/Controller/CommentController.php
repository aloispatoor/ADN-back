<?php

namespace App\Controller;

use App\Entity\User;
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
    public function create(Request $request, ArticleRepository $articleRepository, $id, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $article = $articleRepository->findOneBy([ 'id' => $id ]);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setArticle($article);
            $comment->setAuthor($this->getUser());
            $em->persist($comment);
            $em->flush();
            
            return $this->redirectToRoute('app_article_single');
        }
        
        return $this->renderForm('comment/create.html.twig', [
            'commentform' => $form,
        ]);
    }
}
