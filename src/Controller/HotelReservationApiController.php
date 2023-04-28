<?php

namespace App\Controller;

use App\Entity\HotelReservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/hotel-reservations")
 */
class HotelReservationApiController extends AbstractController
{
    /**
     * @Route("/", name="hotel_reservation_api_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $hotelReservations = $entityManager->getRepository(HotelReservation::class)->findAll();

        return $this->json($hotelReservations);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_show", methods={"GET"})
     */
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $hotelReservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$hotelReservation) {
            return $this->json(['error' => 'Hotel reservation not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($hotelReservation);
    }

    /**
     * @Route("/", name="hotel_reservation_api_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hotelReservation = new HotelReservation();
        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($hotelReservation);
        $entityManager->flush();

        return $this->json($hotelReservation);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $hotelReservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$hotelReservation) {
            return $this->json(['error' => 'Hotel reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(HotelReservationType::class, $hotelReservation);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json($hotelReservation);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $hotelReservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$hotelReservation) {
            return $this->json(['error' => 'Hotel reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($hotelReservation);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}