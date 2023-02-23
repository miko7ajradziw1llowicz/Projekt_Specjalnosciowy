<?php

namespace App\Controller;

use App\Entity\HotelReservation;
use App\Form\HotelReservationType;
use App\Repository\HotelReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// #[Route('/hotel/reservation')]
class HotelReservationController extends AbstractController
{
    #[Route('/', name: 'app_hotel_reservation_index', methods: ['GET'])]
    public function index(HotelReservationRepository $hotelReservationRepository): Response
    {
        return $this->render('hotel_reservation/index.html.twig', [
            'hotel_reservations' => $hotelReservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hotel_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HotelReservationRepository $hotelReservationRepository): Response
    {
        $hotelReservation = new HotelReservation();
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hotelReservationRepository->save($hotelReservation, true);

            return $this->redirectToRoute('app_hotel_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hotel_reservation/new.html.twig', [
            'hotel_reservation' => $hotelReservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_reservation_show', methods: ['GET'])]
    public function show(HotelReservation $hotelReservation): Response
    {
        return $this->render('hotel_reservation/show.html.twig', [
            'hotel_reservation' => $hotelReservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hotel_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HotelReservation $hotelReservation, HotelReservationRepository $hotelReservationRepository): Response
    {
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hotelReservationRepository->save($hotelReservation, true);

            return $this->redirectToRoute('app_hotel_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hotel_reservation/edit.html.twig', [
            'hotel_reservation' => $hotelReservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hotel_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, HotelReservation $hotelReservation, HotelReservationRepository $hotelReservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hotelReservation->getId(), $request->request->get('_token'))) {
            $hotelReservationRepository->remove($hotelReservation, true);
        }

        return $this->redirectToRoute('app_hotel_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
