<?php

namespace App\Controller;

use App\Services\StaffService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * StaffController handles staff-related operations.
 */
#[Route('/staff', name: 'staff_routes')]
class StaffController extends AbstractController
{
    /**
     * @var StaffService
     */
    private StaffService $staffService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param StaffService $staffService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(StaffService $staffService, EntityManagerInterface $entityManager)
    {
        $this->staffService = $staffService;
        $this->entityManager = $entityManager;
    }

    /**
     * Get a list of all staff members.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_staff', methods: ['GET'])]
    public function getStaff(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->staffService->getStaff($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Get a single staff member by their ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_staff_member', methods: ['GET'])]
    public function getStaffMember(int $id): JsonResponse
    {
        $staffMember = $this->staffService->getStaffById($id);
        return new JsonResponse($staffMember, Response::HTTP_OK);
    }

    /**
     * Create a new staff member.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_staff_member', methods: ['POST'])]
    public function createStaffMember(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $staffMember = $this->staffService->createStaff($requestData);
        $this->entityManager->flush();

        return new JsonResponse($staffMember, Response::HTTP_CREATED);
    }

    /**
     * Update an existing staff member.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_staff_member', methods: ['PATCH'])]
    public function updateStaffMember(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $staffMember = $this->staffService->updateStaff($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($staffMember, Response::HTTP_OK);
    }

    /**
     * Delete a staff member by their ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_staff_member', methods: ['DELETE'])]
    public function deleteStaffMember(int $id): JsonResponse
    {
        $this->staffService->deleteStaff($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}