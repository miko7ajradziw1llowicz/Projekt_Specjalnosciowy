<?php

namespace App\Controller;
use App\Form\HotelReservationType;
use App\Entity\HotelReservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
namespace App\Controller;
use App\Entity\HotelReservation;
use App\Form\HotelReservationType;
use App\Repository\HotelReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
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
    
    #[Route('/', name: 'hotel_reservation_api_create', methods: ['POST'])]

    public function createHotelReservation(ManagerRegistry $doctrine, Request $request): Response
    {
        $reservation = new HotelReservation();
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['Name']) || !isset($data['Lastname']) || !isset($data['PhoneNumber']) || !isset($data['HowManyAdultPeople']) || !isset($data['HowManyKids']) || !isset($data['DateFrom']) || !isset($data['DateTo'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }
    
        $reservation->setName($data['Name']);
        $reservation->setLastname($data['Lastname']);
        $reservation->setPhoneNumber($data['PhoneNumber']);
        $reservation->setHowManyAdultPeople($data['HowManyAdultPeople']);
        $reservation->setHowManyKids($data['HowManyKids']);
    
        // set the date fields using DateTimeImmutable to avoid mutation
        $dateFrom = new DateTimeImmutable($data['DateFrom']);
        $dateTo = new DateTimeImmutable($data['DateTo']);
        $reservation->setDateFrom($dateFrom);
        $reservation->setDateTo($dateTo);
    
        $entityManager = $doctrine->getManager();
        $entityManager->getRepository(HotelReservation::class)->save($reservation);
        return new JsonResponse(['GoodGood']);
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