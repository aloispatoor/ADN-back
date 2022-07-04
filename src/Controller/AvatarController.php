<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Repository\AvatarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvatarController extends AbstractController
{
    #[Route('/avatar', name: 'app_avatar_show')]
    public function show(AvatarRepository $avatarRepository, Avatar $avatar): Response
    {
        $avatar = $this->getUser();
        $userAvatar = $avatarRepository->findBy(['user' => $avatar->getUserIdentifier()]);
        return $this->render('avatar/show.html.twig', [
            'avatar' => $userAvatar
        ]);
    }
}
