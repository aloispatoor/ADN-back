<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\AvatarType;
use App\Repository\AvatarRepository;
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
    #[Route('/usersProfile/{id<\d+>}', name: 'app_user_usersprofile', methods: ['GET'])]
    public function usersProfile(ArticleRepository $articleRepository, AvatarRepository $avatarRepository, User $user): Response
    {
        $userAvatar = $avatarRepository->findBy(['user' => $user]);
        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);
        return $this->render('user/usersProfile.html.twig', [
            'articles' => $articles,
            'avatars' => $userAvatar,
            'user' => $user,
            'id' => $user->getId(),
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function profile(ArticleRepository $articleRepository, AvatarRepository $avatarRepository): Response
    {
        $user = $this->getUser();

        $userAvatar = $avatarRepository->findBy(['user' => $user]);
        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);

        return $this->render('user/profile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'avatars' => $userAvatar
        ]);  
    }

    #[Route('/edit/{id<\d+>}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function edit(User $user, AvatarRepository $avatarRepository, Request $request, EntityManagerInterface $em): Response
    {
        $userAvatar = $avatarRepository->findBy(['user' => $user]);
        $avatar = new Avatar();

        $imageForm = $this->createForm(AvatarType::class, $avatar);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $avatar = $imageForm->getData();
            $avatar->setUser($user);
            $em->persist($avatar);
            $em->flush();
        
            $this->addFlash('successEditProfile', 'Informations modifiées avec succès !');

            return $this->redirectToRoute('app_user_profile');
        } elseif ($imageForm == null){
            $avatar->setImage(null);
            $avatar->setUser(null);
            return $this->redirectToRoute('app_user_profile');
        }


        return $this->renderForm('user/edit.html.twig', [
            'imageForm' => $imageForm,
            'avatar' => $avatar,
            'avatars' => $userAvatar,
            'user' => $user,
            ]);
    }
}
