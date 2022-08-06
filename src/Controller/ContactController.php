<?php

namespace App\Controller;

use App\Form\MessageType;
use App\Service\SendMailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact_index')]
    public function index(Request $request, SendMailService $mailer): Response
    {
      
        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contactFormData = $form->getData();
            $subject = 'Demande de contact sur votre site de ' . $contactFormData['email'];
            $content = $contactFormData['firstname'] . $contactFormData['lastname'] . ' vous a envoyé le message suivant: ' . $contactFormData['message'];
            $mailer->receiveEmail(to: 'siteadmin@mailhog.local',from: $contactFormData['email'],subject: $subject, content: $content);

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

