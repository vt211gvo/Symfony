<?php

namespace App\Services;

use App\Entity\Review;
use App\Entity\ServiceOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceOrderService
{
    public const REQUIRED_SERVICE_ORDER_CREATE_FIELDS = [
        'booking',
        'service',
        'price',
        'status',
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
    public function getServiceOrders(): array
    {
        return $this->entityManager->getRepository(ServiceOrder::class)->findAll();
    }


    /**
     * @param int $id
     * @return ServiceOrder
     */
    public function getServiceOrderById(int $id): ServiceOrder
    {
        $serviceOrder = $this->entityManager->getRepository(ServiceOrder::class)->find($id);

        if (!$serviceOrder) {
            throw new NotFoundHttpException('Service order not found');
        }

        return $serviceOrder;
    }


    /**
     * @param array $data
     * @return ServiceOrder
     * @throws \DateMalformedStringException
     */
    public function createServiceOrder(array $data): ServiceOrder
    {
        $this->requestCheckerService::check($data, self::REQUIRED_SERVICE_ORDER_CREATE_FIELDS);

        $serviceOrder = new ServiceOrder();

        return $this->objectHandlerService->saveEntity($serviceOrder, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return ServiceOrder
     * @throws \DateMalformedStringException
     */
    public function updateServiceOrder(int $id, array $data): ServiceOrder
    {
        $serviceOrder = $this->getServiceOrderById($id);

        return $this->objectHandlerService->saveEntity($serviceOrder, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteServiceOrder(int $id): void
    {
        $serviceOrder = $this->getServiceOrderById($id);

        $this->entityManager->remove($serviceOrder);
        $this->entityManager->flush();
    }
}