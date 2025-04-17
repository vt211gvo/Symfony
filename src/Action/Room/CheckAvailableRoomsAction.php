<?php

namespace App\Action\Room;

use App\Entity\Room;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CheckAvailableRoomsAction extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $checkinDate = new \DateTime($request->get('checkinDate'));
        $checkoutDate = new \DateTime($request->get('checkoutDate'));

        if ($checkinDate >= $checkoutDate) {
            return new JsonResponse([
                'error' => 'Checkout date must be later than check-in date.',
            ], 400);
        }

        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
        $availableRooms = [];

        foreach ($rooms as $room) {
            $isAvailable = true;
            $bookings = $this->entityManager->getRepository(Booking::class)->findBy(['room' => $room]);

            foreach ($bookings as $booking) {
                if (($booking->getCheckinDate() < $checkoutDate) && ($booking->getCheckoutDate() > $checkinDate)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableRooms[] = [
                    'id' => $room->getId(),
                    'number' => $room->getNumber(),
                    'type' => $room->getType(),
                    'capacity' => $room->getCapacity(),
                    'price' => $room->getPrice(),
                ];
            }
        }

        if (count($availableRooms) === 0) {
            return new JsonResponse([
                'message' => 'No available rooms for the selected dates.',
            ], 404);
        }

        return new JsonResponse([
            'availableRooms' => $availableRooms,
        ]);
    }
}