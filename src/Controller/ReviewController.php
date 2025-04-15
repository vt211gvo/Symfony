<?php

namespace App\Controller;

use App\Services\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * ReviewController handles review-related operations.
 */
#[Route('/review', name: 'review_routes')]
class ReviewController extends AbstractController
{
    /**
     * @var ReviewService
     */
    private ReviewService $reviewService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ReviewService $reviewService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ReviewService $reviewService, EntityManagerInterface $entityManager)
    {
        $this->reviewService = $reviewService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_reviews', methods: ['GET'])]
    public function getReviews(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->reviewService->getReviews($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_review', methods: ['GET'])]
    public function getReview(int $id): JsonResponse
    {
        $review = $this->reviewService->getReviewById($id);
        return new JsonResponse($review, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_review', methods: ['POST'])]
    public function createReview(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $review = $this->reviewService->createReview($requestData);
        $this->entityManager->flush();

        return new JsonResponse($review, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_review', methods: ['PATCH'])]
    public function updateReview(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $review = $this->reviewService->updateReview($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($review, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_review', methods: ['DELETE'])]
    public function deleteReview(int $id): JsonResponse
    {
        $this->reviewService->deleteReview($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}