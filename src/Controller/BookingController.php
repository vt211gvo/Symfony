<?php

namespace App\Controller;

use App\Services\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param BookingService $bookingService
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_bookings', methods: ['GET'])]
    public function getBookings(): JsonResponse
    {
        $bookings = $this->bookingService->getBookings();
        return new JsonResponse($bookings, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
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
    #[Route('/', name: 'create_booking', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $booking = $this->bookingService->createBooking($requestData);
        return new JsonResponse($booking, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_booking', methods: ['PATCH'])]
    public function updateBooking(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $booking = $this->bookingService->updateBooking($id, $requestData);
        return new JsonResponse($booking, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_booking', methods: ['DELETE'])]
    public function deleteBooking(int $id): JsonResponse
    {
        $this->bookingService->deleteBooking($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
