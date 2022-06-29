<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\UserType;
use App\Form\AvatarType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/usersProfile/{id<\d+>}', name: 'app_user_usersprofile', methods: ['GET'])]
    public function usersProfile(ArticleRepository $articleRepository, User $user): Response
    {
        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);
        return $this->render('user/usersProfile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'id' => $user->getId(),
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function profile(ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();

        // $userAvatar = $avatarRepository->findBy([]);
        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);

        return $this->render('user/profile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            // 'avatar' => $userAvatar
        ]);  
    }

    #[Route('/edit/{id<\d+>}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function edit(User $user, Avatar $avatar, Request $request, EntityManagerInterface $em): Response
    {
            // $form = $this->createForm(UserType::class, $user);
            // $form->handleRequest($request);
            $avatar = new Avatar();

            $imageForm = $this->createForm(AvatarType::class, $avatar);
            $imageForm->handleRequest($request);

            if ($imageForm->isSubmitted() && $imageForm->isValid()) {
                $avatar->setUser($user);
                // $user = $imageForm->getData();
                $em->persist($avatar);
                $em->flush();
            
            // if ($form->isSubmitted() && $form->isValid()) {
            //     $user = $form->getData();
            //     $em->persist($user);
            //     $em->flush();
                $this->addFlash('successEditProfile', 'Informations modifiées avec succès !');
    
                return $this->redirectToRoute('app_user_profile');
            }

        return $this->renderForm('user/edit.html.twig', [
            // 'form' => $form,
            'imageForm' => $imageForm->createView(),
            'avatar' => $avatar,
            'user' => $user,
            ]);
    }
}
