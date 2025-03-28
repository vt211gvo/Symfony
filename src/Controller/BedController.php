<?php

namespace App\Controller;

use App\Services\BedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bed', name: 'bed_routes')]
class BedController extends AbstractController
{
    /**
     * @var BedService
     */
    private BedService $bedService;

    /**
     * @param BedService $bedService
     */
    public function __construct(BedService $bedService)
    {
        $this->bedService = $bedService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_beds', methods: ['GET'])]
    public function getBeds(): JsonResponse
    {
        $beds = $this->bedService->getBeds();
        return new JsonResponse($beds, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_bed', methods: ['GET'])]
    public function getBed(int $id): JsonResponse
    {
        $bed = $this->bedService->getBedById($id);
        return new JsonResponse($bed, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_bed', methods: ['POST'])]
    public function createBed(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $bed = $this->bedService->createBed($requestData);
        return new JsonResponse($bed, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_bed', methods: ['PATCH'])]
    public function updateBed(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $bed = $this->bedService->updateBed($id, $requestData);
        return new JsonResponse($bed, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_bed', methods: ['DELETE'])]
    public function deleteBed(int $id): JsonResponse
    {
        $this->bedService->deleteBed($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
