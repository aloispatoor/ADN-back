<?php

namespace App\Controller;

use DateTime;
use App\Entity\Booking;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
     */
    public function MajEvent(?Booking $calendar, Request $request,ManagerRegistry $doctrine): Response
    {
        // On récuoére les données
        $donnees = json_decode($request->getContent());
        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;
            if(!$calendar){ 
                $calendar = new Booking;

                $code = 201;
            }
            // On hydrate l'objet avec les données 
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new DateTime($donnees->start));
            // Pour une Journée
            // if($donnees->allDay){
            //     $calendar->setEnd(new DateTime($donnees->start));
            // }else{
            //     $calendar->setEnd(new DateTime($donnees->end));
            // }
            $calendar->setEnd(new DateTime($donnees->end));
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            $em = $doctrine->getManager();
            $em->persist($calendar);
            $em->flush();

            return new Response('Ok', $code);
        }else{
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}