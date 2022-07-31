<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\GenderRepository;
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
    public function usersProfile(ArticleRepository $articleRepository, GenderRepository $genderRepository, User $user): Response
    {
        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);
        $genders = $genderRepository->findBy(['users' => $user], ['id' => 'ASC']);
        return $this->render('user/usersProfile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'id' => $user->getId(),
            'genders' => $genders
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function profile(ArticleRepository $articleRepository, GenderRepository $genderRepository): Response
    {
        $user = $this->getUser();

        $articles = $articleRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);
        $genders = $genderRepository->findBy(array('users' => array($user)));

        if(!$genders){
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'articles' => $articles,
            'user' => $user,
            'genders' => $genders
        ]);  
    }

    #[Route('/edit/{id<\d+>}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {

        $imageForm = $this->createForm(UserType::class, $user);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $em->persist($user);
            $em->flush();
        
            $this->addFlash('successEditProfile', 'Informations modifiées avec succès !');

            return $this->redirectToRoute('app_user_profile');
        } elseif ($imageForm == null){
            
            return $this->redirectToRoute('app_user_profile');
        }


        return $this->renderForm('user/edit.html.twig', [
            'imageForm' => $imageForm,
            'user' => $user,
            ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_user_delete')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté en tant qu\'utilisateu-rice pour accéder à cette page')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($user) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('deleteAccount', 'Votre compte a été supprimé avec succès');

            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_user_profile');
    }

}
