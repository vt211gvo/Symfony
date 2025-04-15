<?php

namespace App\Controller;

use App\Services\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * BookingController handles booking-related operations.
 */
#[Route('/booking', name: 'booking_routes')]
class BookingController extends AbstractController
{
    /**
     * @var BookingService
     */
    private BookingService $bookingService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param BookingService $bookingService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BookingService $bookingService, EntityManagerInterface $entityManager)
    {
        $this->bookingService = $bookingService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_bookings', methods: ['GET'])]
    public function getBookings(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->bookingService->getBookings($requestData, $itemsPerPage, $page);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_booking', methods: ['GET'])]
    public function getBooking(int $id): JsonResponse
    {
        $booking = $this->bookingService->getBookingById($id);
        return new JsonResponse($booking, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_booking', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $booking = $this->bookingService->createBooking($requestData);
        $this->entityManager->flush();

        return new JsonResponse($booking, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_booking', methods: ['PATCH'])]
    public function updateBooking(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $booking = $this->bookingService->updateBooking($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($booking, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_booking', methods: ['DELETE'])]
    public function deleteBooking(int $id): JsonResponse
    {
        $this->bookingService->deleteBooking($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
