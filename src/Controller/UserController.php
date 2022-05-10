<?php

namespace App\Controller;

use App\Entity\User;
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
    #[Route('/usersProfile/{id<\d+>}', name: 'app_user_usersprofile')]
    public function usersProfile(ArticleRepository $articleRepository, User $user): Response
    {
        $articles = $articleRepository->findBy(['author' => $user->getUserIdentifier()], ['createdAt' => 'DESC']);
        return $this->render('user/usersProfile.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }

    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function profile(User $user, ArticleRepository $articleRepository): Response
    {
        
        $user = $this->getUser();
        $articles = $articleRepository->findBy(['author' => $user->getUserIdentifier()], ['createdAt' => 'DESC']);

        return $this->render('user/profile.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);  
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/edit/{id<\d+>}', name: 'app_user_edit')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $user) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            // return $this->redirectToRoute('app_user_profile');
        }

        if ($user->getPlainPassword() !== null) {
            $user->setPassword(
                $this->userPasswordEncoder->encode(
                $user->getPlainPassword(),
                $user
            ));
            return $this->redirectToRoute('app_user_profile');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Informations modifiées avec succès !');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->renderForm('user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
            'action' => 'Edit'
            ]);
    }
}
