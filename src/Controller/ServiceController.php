<?php

namespace App\Controller;

use App\Services\ServiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ServiceService $serviceService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ServiceService $serviceService, EntityManagerInterface $entityManager)
    {
        $this->serviceService = $serviceService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_services', methods: ['GET'])]
    public function getServices(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->serviceService->getServices($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
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
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_service', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $service = $this->serviceService->createService($requestData);
        $this->entityManager->flush();

        return new JsonResponse($service, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_service', methods: ['PATCH'])]
    public function updateService(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $service = $this->serviceService->updateService($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($service, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $this->serviceService->deleteService($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
