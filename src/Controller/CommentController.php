<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment_index')]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('article/single.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/comment/create', name: 'app_comment_create')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $em->persist($comment);
            $em->flush();
            
            return $this->redirectToRoute('app_article_single');
        }
        
        return $this->renderForm('article/single.html.twig', [
            'form' => $form
        ]);
    }
}
