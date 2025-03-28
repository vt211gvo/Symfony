<?php

namespace App\Services;

use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GuestService
{
    public const REQUIRED_GUEST_CREATE_FIELDS = [
        'number',
        'type',
        'capacity',
        'price',
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
    public function getGuests(): array
    {
        return $this->entityManager->getRepository(Guest::class)->findAll();
    }


    /**
     * @param int $id
     * @return Guest
     */
    public function getGuestById(int $id): Guest
    {
        $guest = $this->entityManager->getRepository(Guest::class)->find($id);

        if (!$guest) {
            throw new NotFoundHttpException('Guest not found');
        }

        return $guest;
    }


    /**
     * @param array $data
     * @return Guest
     * @throws \DateMalformedStringException
     */
    public function createGuest(array $data): Guest
    {
        $this->requestCheckerService::check($data, self::REQUIRED_GUEST_CREATE_FIELDS);

        $guest = new Guest();

        return $this->objectHandlerService->saveEntity($guest, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Guest
     * @throws \DateMalformedStringException
     */
    public function updateGuest(int $id, array $data): Guest
    {
        $guest = $this->getGuestById($id);

        return $this->objectHandlerService->saveEntity($guest, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteGuest(int $id): void
    {
        $guest = $this->getGuestById($id);

        $this->entityManager->remove($guest);
        $this->entityManager->flush();
    }
}