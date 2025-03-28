<?php

namespace App\Controller;

use App\Services\StaffService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param StaffService $staffService
     */
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Get a list of all staff members.
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_staff', methods: ['GET'])]
    public function getStaff(): JsonResponse
    {
        $staffMembers = $this->staffService->getStaff();
        return new JsonResponse($staffMembers, Response::HTTP_OK);
    }

    /**
     * Get a single staff member by their ID.
     *
     * @param int $id
     * @return JsonResponse
     */
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
    #[Route('/', name: 'create_staff_member', methods: ['POST'])]
    public function createStaffMember(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $staffMember = $this->staffService->createStaff($requestData);
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
    #[Route('/{id}', name: 'update_staff_member', methods: ['PATCH'])]
    public function updateStaffMember(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $staffMember = $this->staffService->updateStaff($id, $requestData);
        return new JsonResponse($staffMember, Response::HTTP_OK);
    }

    /**
     * Delete a staff member by their ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_staff_member', methods: ['DELETE'])]
    public function deleteStaffMember(int $id): JsonResponse
    {
        $this->staffService->deleteStaff($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}