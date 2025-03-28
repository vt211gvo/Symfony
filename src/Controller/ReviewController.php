<?php

namespace App\Controller;

use App\Services\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param ReviewService $reviewService
     */
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_reviews', methods: ['GET'])]
    public function getReviews(): JsonResponse
    {
        $reviews = $this->reviewService->getReviews();
        return new JsonResponse($reviews, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
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
    #[Route('/', name: 'create_review', methods: ['POST'])]
    public function createReview(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $review = $this->reviewService->createReview($requestData);
        return new JsonResponse($review, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_review', methods: ['PATCH'])]
    public function updateReview(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $review = $this->reviewService->updateReview($id, $requestData);
        return new JsonResponse($review, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_review', methods: ['DELETE'])]
    public function deleteReview(int $id): JsonResponse
    {
        $this->reviewService->deleteReview($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}