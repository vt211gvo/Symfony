<?php

namespace App\Controller;

use App\Services\RoomService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * RoomController handles room-related operations.
 */
#[Route('/room', name: 'room_routes')]
class RoomController extends AbstractController
{
    /**
     * @var RoomService
     */
    private RoomService $roomService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param RoomService $roomService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RoomService $roomService, EntityManagerInterface $entityManager)
    {
        $this->roomService = $roomService;
        $this->entityManager = $entityManager;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_rooms', methods: ['GET'])]
    public function getRooms(): JsonResponse
    {
        $rooms = $this->roomService->getRooms();
        return new JsonResponse($rooms, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_room', methods: ['GET'])]
    public function getRoom(int $id): JsonResponse
    {
        $room = $this->roomService->getRoomById($id);
        return new JsonResponse($room, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_room', methods: ['POST'])]
    public function createRoom(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $room = $this->roomService->createRoom($requestData);
        $this->entityManager->flush();

        return new JsonResponse($room, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_room', methods: ['PATCH'])]
    public function updateRoom(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $room = $this->roomService->updateRoom($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($room, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_room', methods: ['DELETE'])]
    public function deleteRoom(int $id): JsonResponse
    {
        $this->roomService->deleteRoom($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
