<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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
        $articles = $articleRepository->findAll();
        return $this->render('super_admin/articles.html.twig', [
            'articles' => $articles,
        ]);
    }
}
