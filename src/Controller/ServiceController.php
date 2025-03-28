<?php

namespace App\Controller;

use App\Services\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ServiceController handles service-related operations.
 */
#[Route('/service', name: 'service_routes')]
class ServiceController extends AbstractController
{
    /**
     * @var ServiceService
     */
    private ServiceService $serviceService;

    /**
     * @param ServiceService $serviceService
     */
    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_services', methods: ['GET'])]
    public function getServices(): JsonResponse
    {
        $services = $this->serviceService->getServices();
        return new JsonResponse($services, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_service', methods: ['GET'])]
    public function getService(int $id): JsonResponse
    {
        $service = $this->serviceService->getServiceById($id);
        return new JsonResponse($service, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_service', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $service = $this->serviceService->createService($requestData);
        return new JsonResponse($service, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_service', methods: ['PATCH'])]
    public function updateService(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $service = $this->serviceService->updateService($id, $requestData);
        return new JsonResponse($service, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $this->serviceService->deleteService($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
