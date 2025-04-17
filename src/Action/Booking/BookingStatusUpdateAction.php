<?php

namespace App\Action\Booking;

use App\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class BookingStatusUpdateAction extends AbstractController
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
     * @param Booking $booking
     * @return JsonResponse
     */
    public function __invoke(Request $request, Booking $booking): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) {
            $newStatus = $data['status'];
            $booking->setStatus($newStatus);

            $this->entityManager->flush();

            return new JsonResponse([
                'bookingId' => $booking->getId(),
                'newStatus' => $booking->getStatus(),
            ]);
        }

        return new JsonResponse([
            'error' => 'Status not provided',
        ], 400);
    }

}