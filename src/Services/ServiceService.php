<?php

namespace App\Services;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceService
{
    public const REQUIRED_SERVICE_CREATE_FIELDS = [
        'name',
        'description',
        'price',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;

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
    public function getServices(): array
    {
        return $this->entityManager->getRepository(Service::class)->findAll();
    }


    /**
     * @param int $id
     * @return Service
     */
    public function getServiceById(int $id): Service
    {
        $service = $this->entityManager->getRepository(Service::class)->find($id);

        if (!$service) {
            throw new NotFoundHttpException('Service order not found');
        }

        return $service;
    }


    /**
     * @param array $data
     * @return Service
     * @throws \DateMalformedStringException
     */
    public function createService(array $data): Service
    {
        $this->requestCheckerService::check($data, self::REQUIRED_SERVICE_CREATE_FIELDS);

        $service = new Service();

        return $this->objectHandlerService->saveEntity($service, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Service
     * @throws \DateMalformedStringException
     */
    public function updateService(int $id, array $data): Service
    {
        $service = $this->getServiceById($id);

        return $this->objectHandlerService->saveEntity($service, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteService(int $id): void
    {
        $service = $this->getServiceById($id);

        $this->entityManager->remove($service);
        $this->entityManager->flush();
    }
}