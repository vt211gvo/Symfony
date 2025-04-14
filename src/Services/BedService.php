<?php

namespace App\Services;

use App\Entity\Bed;
use App\Repository\BedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BedService
{
    public const REQUIRED_BED_CREATE_FIELDS = [
        'room',
        'bedNumber',
    ];

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestCheckerService;
    /**
     * @var ObjectHandlerService
     */
    private ObjectHandlerService $objectHandlerService;

    /**
     * @var BedRepository
     */
    private BedRepository $bedRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     * @param BedRepository $bedRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService,
        BedRepository $bedRepository
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
        $this->bedRepository = $bedRepository;
    }

    /**
     * @param array $filters
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getBeds(array $filters, int $itemsPerPage, int $page): array
    {
        return $this->bedRepository->getAllByFilter($filters, $itemsPerPage, $page);
    }


    /**
     * @param int $id
     * @return Bed
     */
    public function getBedById(int $id): Bed
    {
        $bed = $this->entityManager->getRepository(Bed::class)->find($id);

        if (!$bed) {
            throw new NotFoundHttpException('Bed not found');
        }

        return $bed;
    }


    /**
     * @param array $data
     * @return Bed
     * @throws \DateMalformedStringException
     */
    public function createBed(array $data): Bed
    {
        $this->requestCheckerService::check($data, self::REQUIRED_BED_CREATE_FIELDS);

        $bed = new Bed();

        return $this->objectHandlerService->saveEntity($bed, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Bed
     * @throws \DateMalformedStringException
     */
    public function updateBed(int $id, array $data): Bed
    {
        $bed = $this->getBedById($id);

        return $this->objectHandlerService->saveEntity($bed, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteBed(int $id): void
    {
        $bed = $this->getBedById($id);

        $this->entityManager->remove($bed);
        $this->entityManager->flush();
    }
}