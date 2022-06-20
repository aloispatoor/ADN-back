<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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
        $articles = $articleRepository->findBy(['author' => $user->getUserIdentifier()], ['createdAt' => 'DESC']);
        $userAvatar = $avatarRepository->findBy(['user' => $user->getUserIdentifier()]);
        return $this->render('user/usersProfile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'avatar' => $userAvatar
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function profile(ArticleRepository $articleRepository, AvatarRepository $avatarRepository): Response
    {
        $user = $this->getUser();

        $userAvatar = $avatarRepository->findBy([]);
        $articles = $articleRepository->findBy(['author' => $user->getUserIdentifier()], ['createdAt' => 'DESC']);

        return $this->render('user/profile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'avatar' => $userAvatar
        ]);  
    }

    #[Route('/edit/{id<\d+>}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, $id, AvatarRepository $avatarRepository): Response
    {
        if ($this->getUser()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Informations modifiées avec succès !');
    
                return $this->redirectToRoute('app_user_profile');
            }
        }
        if ($user->getPlainPassword() !== null) {
            $user->setPassword(
                $this->userPasswordEncoder->encode(
                $user->getPlainPassword(),
                $user
            ));
            return $this->redirectToRoute('app_user_profile');
        }

        if($this->getUser()) {
            $imageForm = $this->createForm(AvatarType::class);
            $avatar = $avatarRepository->findOneBy([ 'id' => $id ]);
            $imageForm->handleRequest($request);
            if ($imageForm->isSubmitted() && $imageForm->isValid()) {
                $user->setAvatar($avatar);
                $user = $imageForm->getData();
                $em->persist($user);
                $em->flush();
    
                return $this->redirectToRoute('app_user_profile');
            }
        }

        return $this->renderForm('user/edit.html.twig', [
            'form' => $form,
            'imageForm' => $imageForm,
            'avatar' => $avatar,
            'user' => $user,
            'action' => 'Edit'
            ]);
    }
}
