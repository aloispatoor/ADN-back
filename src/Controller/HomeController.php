<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookingRepository $bookingRepository, ArticleRepository $articleRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
            'articles' => $articleRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
