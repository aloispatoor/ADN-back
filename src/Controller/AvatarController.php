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

    // #[Route('/avatar/edit/{id<\d+>}', name: 'app_avatar_edit')]
    // public function edit(Request $request, Avatar $avatar, EntityManagerInterface $em): Response
    // {
    //     $imageForm = $this->createForm(AvatarType::class, $avatar);
    //     $imageForm->handleRequest($request);

    //     if ($this->getUser()) {
    //         if ($imageForm->isSubmitted() && $imageForm->isValid()) {
    //             // $user->setAvatar($avatar);
    //             // $user = $imageForm->getData();
    //             // $em->persist($user);
    //             $em->flush();
    //         }
    
    //         return $this->redirectToRoute('app_user_profile');
    //         }

    //     return $this->renderForm('avatar/edit.html.twig', [
    //         'imageForm' => $imageForm,
    //         'avatar' => $avatar,
    //         'action' => 'Edit'
    //         ]);
    // }
}
