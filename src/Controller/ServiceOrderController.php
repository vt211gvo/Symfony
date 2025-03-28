<?php

namespace App\Controller;

use App\Services\ServiceOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ServiceOrderController handles service order-related operations.
 */
#[Route('/service-order', name: 'service_order_routes')]
class ServiceOrderController extends AbstractController
{
    /**
     * @var ServiceOrderService
     */
    private ServiceOrderService $serviceOrderService;

    /**
     * @param ServiceOrderService $serviceOrderService
     */
    public function __construct(ServiceOrderService $serviceOrderService)
    {
        $this->serviceOrderService = $serviceOrderService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_service_orders', methods: ['GET'])]
    public function getServiceOrders(): JsonResponse
    {
        $serviceOrders = $this->serviceOrderService->getServiceOrders();
        return new JsonResponse($serviceOrders, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_service_order', methods: ['GET'])]
    public function getServiceOrder(int $id): JsonResponse
    {
        $serviceOrder = $this->serviceOrderService->getServiceOrderById($id);
        return new JsonResponse($serviceOrder, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_service_order', methods: ['POST'])]
    public function createServiceOrder(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $serviceOrder = $this->serviceOrderService->createServiceOrder($requestData);
        return new JsonResponse($serviceOrder, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_service_order', methods: ['PATCH'])]
    public function updateServiceOrder(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $serviceOrder = $this->serviceOrderService->updateServiceOrder($id, $requestData);
        return new JsonResponse($serviceOrder, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_service_order', methods: ['DELETE'])]
    public function deleteServiceOrder(int $id): JsonResponse
    {
        $this->serviceOrderService->deleteServiceOrder($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
