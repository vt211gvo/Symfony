<?php

namespace App\Controller;

use App\Services\GuestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GuestController handles guest-related operations.
 */
#[Route('/guest', name: 'guest_routes')]
class GuestController extends AbstractController
{
    /**
     * @var GuestService
     */
    private GuestService $guestService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param GuestService $guestService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(GuestService $guestService, EntityManagerInterface $entityManager)
    {
        $this->guestService = $guestService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'get_guests', methods: ['GET'])]
    public function getGuests(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->guestService->getGuests();
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_guest', methods: ['GET'])]
    public function getGuest(int $id): JsonResponse
    {
        $guest = $this->guestService->getGuestById($id);
        return new JsonResponse($guest, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_guest', methods: ['POST'])]
    public function createGuest(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $guest = $this->guestService->createGuest($requestData);
        $this->entityManager->flush();

        return new JsonResponse($guest, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_guest', methods: ['PATCH'])]
    public function updateGuest(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $guest = $this->guestService->updateGuest($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($guest, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_guest', methods: ['DELETE'])]
    public function deleteGuest(int $id): JsonResponse
    {
        $this->guestService->deleteGuest($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
