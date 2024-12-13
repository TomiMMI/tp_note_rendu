<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ReservationController extends AbstractController
{

    #[Route('/api/reservations', name: 'api_reservation_list', methods: ['GET'])]
    public function listReservations(ReservationRepository $reservationRepository): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }
        $reservations = $reservationRepository->findAll();

        return $this->render('reservation/admin_list.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/api/reservations', name: 'api_reservation_create', methods: ['POST'])]
    public function createReservation(Request $request, EntityManagerInterface $em, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation : une seule réservation par plage horaire pour une même date
            $existingReservation = $reservationRepository->findOneBy([
                'date' => $reservation->getDate(),
                'timeSlot' => $reservation->getTimeSlot(),
            ]);

            if ($existingReservation) {
                $this->addFlash('error', 'This time slot is already reserved for the selected date.');
                return $this->redirectToRoute('reservation_create');
            }

            $now = new \DateTime();
            $reservationDate = $reservation->getDate();
            $interval = $now->diff($reservationDate);

            if ($interval->invert || $interval->days < 1) {
                $this->addFlash('error', 'Reservations must be made at least 24 hours in advance.');
                return $this->redirectToRoute('reservation_create');
            }

            $reservation->setUser($this->getUser());
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Reservation successfully created.');
            return $this->redirectToRoute('user_reservation_list');
        }

        return $this->render('reservation/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/api/user/reservations', name: 'api_user_reservation_list', methods: ['GET'])]
    public function userReservations(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['user' => $user]);

        return $this->render('reservation/user_list.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/api/user/{id}/delete', name: 'admin_reservation_delete', methods: ['DELETE'])]
    public function deleteReservation(Reservation $reservation, EntityManagerInterface $em): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }

        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success', 'Reservation successfully deleted.');
        return $this->redirectToRoute('admin_reservation_list');
    }
}