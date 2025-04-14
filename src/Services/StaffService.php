<?php

namespace App\Services;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaffService
{
    public const REQUIRED_STAFF_CREATE_FIELDS = [
        'name',
        'position',
        'salary',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;
    private StaffRepository $staffRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     * @param StaffRepository $staffRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService,
        StaffRepository $staffRepository
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
        $this->staffRepository = $staffRepository;
    }

    /**
     * @param array $filters
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getStaff(array $filters, int $itemsPerPage, int $page): array
    {
        return $this->staffRepository->getAllByFilter($filters, $itemsPerPage, $page);
    }


    /**
     * @param int $id
     * @return Staff
     */
    public function getStaffById(int $id): Staff
    {
        $staff = $this->entityManager->getRepository(Staff::class)->find($id);

        if (!$staff) {
            throw new NotFoundHttpException('Staff order not found');
        }

        return $staff;
    }


    /**
     * @param array $data
     * @return Staff
     * @throws \DateMalformedStringException
     */
    public function createStaff(array $data): Staff
    {
        $this->requestCheckerService::check($data, self::REQUIRED_STAFF_CREATE_FIELDS);

        $staff = new Staff();

        return $this->objectHandlerService->saveEntity($staff, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Staff
     * @throws \DateMalformedStringException
     */
    public function updateStaff(int $id, array $data): Staff
    {
        $staff = $this->getStaffById($id);

        return $this->objectHandlerService->saveEntity($staff, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteStaff(int $id): void
    {
        $staff = $this->getStaffById($id);

        $this->entityManager->remove($staff);
        $this->entityManager->flush();
    }
}