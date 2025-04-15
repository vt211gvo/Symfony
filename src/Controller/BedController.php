<?php

namespace App\Controller;

use App\Services\BedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bed', name: 'bed_routes')]
class BedController extends AbstractController
{
    /**
     * @var BedService
     */
    private BedService $bedService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    /**
     * @param BedService $bedService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BedService $bedService, EntityManagerInterface $entityManager)
    {
        $this->bedService = $bedService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_beds', methods: ['GET'])]
    public function getBeds(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->bedService->getBeds($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
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
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_bed', methods: ['POST'])]
    public function createBed(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $bed = $this->bedService->createBed($requestData);
        $this->entityManager->flush();

        return new JsonResponse($bed, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_bed', methods: ['PATCH'])]
    public function updateBed(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $bed = $this->bedService->updateBed($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($bed, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_bed', methods: ['DELETE'])]
    public function deleteBed(int $id): JsonResponse
    {
        $this->bedService->deleteBed($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
