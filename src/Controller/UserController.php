<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{id}<\d+>}', name: 'app_user_profile')]
    // #[IsGranted('ROLE_ADMIN', message: 'Vous devez être connecté en tant que membre de l\'association pour accéder à cette page')]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->renderForm('user/index.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}
