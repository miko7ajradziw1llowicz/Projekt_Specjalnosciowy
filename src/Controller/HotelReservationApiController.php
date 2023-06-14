<?php

namespace App\Controller;

use App\Entity\HotelReservation;
use App\Form\HotelReservationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;

/**
 * @Route("/api/hotel-reservations")
 */
class HotelReservationApiController extends AbstractController
{
    /**
     * @Route("/", name="hotel_reservation_api_index", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $hotelReservations = $doctrine->getRepository(HotelReservation::class)->findAll();

        return $this->json($hotelReservations);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $hotelReservation = $doctrine->getRepository(HotelReservation::class)->find($id);

        if (!$hotelReservation) {
            return $this->json(['error' => 'Hotel reservation not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($hotelReservation);
    }

    /**
     * @Route("/", name="hotel_reservation_api_create", methods={"POST"})
     */
    public function createHotelReservation(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $reservation = new HotelReservation();
        $data = json_decode($request->getContent(), true);

        if (
            !isset($data['Name']) || !isset($data['Lastname']) ||
            !isset($data['PhoneNumber']) || !isset($data['HowManyAdultPeople']) ||
            !isset($data['HowManyKids']) || !isset($data['DateFrom']) || !isset($data['DateTo'])
        ) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $reservation->setName($data['Name']);
        $reservation->setLastname($data['Lastname']);
        $reservation->setPhoneNumber($data['PhoneNumber']);
        $reservation->setHowManyAdultPeople($data['HowManyAdultPeople']);
        $reservation->setHowManyKids($data['HowManyKids']);

        // Set the date fields using DateTimeImmutable to avoid mutation
        $dateFrom = new DateTimeImmutable($data['DateFrom']);
        $dateTo = new DateTimeImmutable($data['DateTo']);
        $reservation->setDateFrom($dateFrom);
        $reservation->setDateTo($dateTo);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        $message = sprintf(
            'Reservation for %s %s has been added. Price is: %s PLN',
            $reservation->getName(),
            $reservation->getLastname(),
            $reservation->getPrice()
        );

        return new JsonResponse(['message' => $message], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_update", methods={"PUT"})
     */
    public function updateHotelReservation(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['Name'])) {
            $reservation->setName($data['Name']);
        }
        if (isset($data['Lastname'])) {
            $reservation->setLastname($data['Lastname']);
        }
        if (isset($data['PhoneNumber'])) {
            $reservation->setPhoneNumber($data['PhoneNumber']);
        }
        if (isset($data['HowManyAdultPeople'])) {
            $reservation->setHowManyAdultPeople($data['HowManyAdultPeople']);
        }
        if (isset($data['HowManyKids'])) {
            $reservation->setHowManyKids($data['HowManyKids']);
        }
        if (isset($data['DateFrom'])) {
            $dateFrom = new DateTimeImmutable($data['DateFrom']);
            $reservation->setDateFrom($dateFrom);
        }
        if (isset($data['DateTo'])) {
            $dateTo = new DateTimeImmutable($data['DateTo']);
            $reservation->setDateTo($dateTo);
        }

        $entityManager->flush();

        $message = sprintf('Reservation with ID %d has been updated', $id);
        return $this->json(['message' => $message]);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_patch", methods={"PATCH"})
     */
    public function patchHotelReservation(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'Name':
                    $reservation->setName($value);
                    break;
                case 'Lastname':
                    $reservation->setLastname($value);
                    break;
                case 'PhoneNumber':
                    $reservation->setPhoneNumber($value);
                    break;
                case 'HowManyAdultPeople':
                    $reservation->setHowManyAdultPeople($value);
                    break;
                case 'HowManyKids':
                    $reservation->setHowManyKids($value);
                    break;
                case 'DateFrom':
                    $dateFrom = new DateTimeImmutable($value);
                    $reservation->setDateFrom($dateFrom);
                    break;
                case 'DateTo':
                    $dateTo = new DateTimeImmutable($value);
                    $reservation->setDateTo($dateTo);
                    break;
                default:
                    break;
            }
        }

        $entityManager->flush();

        $message = sprintf('Reservation with ID %d has been patched', $id);
        return $this->json(['message' => $message]);
    }

    /**
     * @Route("/{id}", name="hotel_reservation_api_delete", methods={"DELETE"})
     */
    public function deleteHotelReservation(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(HotelReservation::class)->find($id);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        $message = sprintf('Reservation with ID %d has been deleted', $id);
        return new JsonResponse(['message' => $message]);
    }
}
