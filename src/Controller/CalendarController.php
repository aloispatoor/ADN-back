<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar_index')]
    public function index(BookingRepository $calendar, ManagerRegistry $comp): Response
    {
        $bookings = $calendar->findAll();

        $rdv = [];

        foreach($bookings as $event) {
            $rdv[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d'),
                'end' => $event->getEnd()->format('Y-m-d'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        $data = json_encode($rdv);
        $bookings = $comp->getRepository(Booking::class)->findBy([], ['start' => 'ASC']);
   
        return $this->render('calendar/index.html.twig', compact('data', 'bookings'));
    }
}
