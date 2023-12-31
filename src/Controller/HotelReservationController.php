<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\HotelReservation;
use App\Form\HotelReservationType;
use App\Repository\HotelReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class HotelReservationController extends AbstractController
{
    private function calculatePrice(HotelReservation $hotelReservation): int
    {
        $diff = date_diff($hotelReservation->getDateFrom(), $hotelReservation->getDateTo())->format("%d");
        $price = ($hotelReservation->getHowManyAdultPeople() * 50 + $hotelReservation->getHowManyKids() * 35) * intval($diff);

        return $price;
    }

    #[Route('/', name: 'app_hotel_reservation_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine, HotelReservationRepository $hotelReservationRepository, Request $request): Response
    {
        $sortBy = $request->query->get('sort_by', 'id'); // Sortuj domyślnie według ID, ale możesz wybrać inne pola

        $hotelReservations = $hotelReservationRepository->findBy([], [$sortBy => 'ASC']);

        return $this->render('hotel_reservation/index.html.twig', [
            'hotel_reservations' => $hotelReservations,
        ]);
    }

    #[Route('/new', name: 'app_hotel_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HotelReservationRepository $hotelReservationRepository): Response
    {
        $hotelReservation = new HotelReservation();
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Oblicz cenę i przypisz do pola price
            $price = $this->calculatePrice($hotelReservation);
            $hotelReservation->setPrice($price);

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
            'getPrice' => $hotelReservation->getPrice(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hotel_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HotelReservation $hotelReservation, HotelReservationRepository $hotelReservationRepository): Response
    {
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Oblicz cenę i przypisz do pola price
            $price = $this->calculatePrice($hotelReservation);
            $hotelReservation->setPrice($price);

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
        if ($this->isCsrfTokenValid('delete' . $hotelReservation->getId(), $request->request->get('_token'))) {
            $hotelReservationRepository->remove($hotelReservation, true);
        }

        return $this->redirectToRoute('app_hotel_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/hotel-reservations', name: 'app_hotel_reservation_api', methods: ['POST'])]
    public function createReservation(Request $request, HotelReservationRepository $hotelReservationRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hotelReservation = new HotelReservation();
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // Oblicz cenę i przypisz do pola price
            $price = $this->calculatePrice($hotelReservation);
            $hotelReservation->setPrice($price);

            $hotelReservationRepository->save($hotelReservation, true);

            return new JsonResponse([
                'success' => true,
                'id' => $hotelReservation->getId(),
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'errors' => (string) $form->getErrors(true),
        ], 400);
    }
}
