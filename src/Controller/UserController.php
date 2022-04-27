<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{id}<\d+>}', name: 'app_user_profile')]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être connecté en tant que membre de l\'association pour accéder à cette page')]
    public function profile(ArticleRepository $articleRepository): Response
    {
        
        $user = $this->getUser();
        $articles = $articleRepository->findBy(['author' => $user->getUserIdentifier()], ['createdAt' => 'DESC']);

        return $this->render('profile_company/company.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
        
    }
}
