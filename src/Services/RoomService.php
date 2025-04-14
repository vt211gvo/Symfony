<?php

namespace App\Services;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoomService
{
    public const REQUIRED_ROOM_CREATE_FIELDS = [
        'number',
        'type',
        'capacity',
        'price',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;
    private RoomRepository $roomRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     * @param RoomRepository $roomRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService,
        RoomRepository $roomRepository
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
        $this->roomRepository = $roomRepository;
    }


    /**
     * @param array $filters
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getRooms(array $filters, int $itemsPerPage, int $page): array
    {
        return $this->roomRepository->getAllByFilter($filters, $itemsPerPage, $page);
    }


    /**
     * @param int $id
     * @return Room
     */
    public function getRoomById(int $id): Room
    {
        $room = $this->entityManager->getRepository(Room::class)->find($id);

        if (!$room) {
            throw new NotFoundHttpException('Room not found');
        }

        return $room;
    }


    /**
     * @param array $data
     * @return Room
     * @throws \DateMalformedStringException
     */
    public function createRoom(array $data): Room
    {
        $this->requestCheckerService::check($data, self::REQUIRED_ROOM_CREATE_FIELDS);

        $room = new Room();

        return $this->objectHandlerService->saveEntity($room, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Room
     * @throws \DateMalformedStringException
     */
    public function updateRoom(int $id, array $data): Room
    {
        $room = $this->getRoomById($id);

        return $this->objectHandlerService->saveEntity($room, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteRoom(int $id): void
    {
        $room = $this->getRoomById($id);

        $this->entityManager->remove($room);
        $this->entityManager->flush();
    }
}