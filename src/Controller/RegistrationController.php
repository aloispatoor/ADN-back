<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register_user')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //JWT generation
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            //Sending Mail
            $mail->sendEmail(
                'apatoor@xyfl3645.odns.fr',
                $user->getEmail(),
                'Activation de votre compte sur le site ADN',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name:'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);

            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addflash('success', 'Votre compte est activé');

                return $this->redirectToRoute('app_article_index');
            }
        }

        $this->addflash('danger', 'Le lien est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/retoken', name:'retoken')]
    public function resendToken(JWTService $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
            $this->addflash('danger', 'Vous devez être connecté-e accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if($user->getIsVerified()){
            $this->addflash('warning', "Cet utilisateur dispose d'un compte activé");
            return $this->redirectToRoute('app_article_index');
        }

        //JWT generation
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        //Sending Mail
        $mail->sendEmail(
            'apatoor@xyfl3645.odns.fr',
            $user->getEmail(),
            'Activation de votre compte sur le site ADN - Au-delà des Normes',
            'register',
            compact('user', 'token')
        );

        $this->addflash('success', 'Le mail a bien été renvoyé');
        return $this->redirectToRoute('app_article_index');
    }
}