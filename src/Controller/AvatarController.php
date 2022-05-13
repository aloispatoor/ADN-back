<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    #[Route('/avatar', name: 'app_avatar_show')]
    public function show(): Response
    {
        return $this->render('avatar/show.html.twig', [
            'controller_name' => 'AvatarController',
        ]);
    }
}
