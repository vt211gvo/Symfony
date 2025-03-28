<?php

namespace App\Services;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    public const REQUIRED_BOOKING_CREATE_FIELDS = [
        'guest',
        'room',
        'checkInDate',
        'checkOutDate',
        'status',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
    }


    /**
     * @return array
     */
    public function getBookings(): array
    {
        return $this->entityManager->getRepository(Booking::class)->findAll();
    }


    /**
     * @param int $id
     * @return Booking
     */
    public function getBookingById(int $id): Booking
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            throw new NotFoundHttpException('Booking not found');
        }

        return $booking;
    }


    /**
     * @param array $data
     * @return Booking
     * @throws \DateMalformedStringException
     */
    public function createBooking(array $data): Booking
    {
        $this->requestCheckerService::check($data, self::REQUIRED_BOOKING_CREATE_FIELDS);

        $booking = new Booking();

        return $this->objectHandlerService->saveEntity($booking, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Booking
     * @throws \DateMalformedStringException
     */
    public function updateBooking(int $id, array $data): Booking
    {
        $booking = $this->getBookingById($id);

        return $this->objectHandlerService->saveEntity($booking, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteBooking(int $id): void
    {
        $booking = $this->getBookingById($id);

        $this->entityManager->remove($booking);
        $this->entityManager->flush();
    }
}