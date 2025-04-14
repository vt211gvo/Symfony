<?php

namespace App\Services;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewService
{
    public const REQUIRED_REVIEW_CREATE_FIELDS = [
        'booking',
        'rating',
        'comment',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;
    private ReviewRepository $reviewRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService,
        ReviewRepository $reviewRepository
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param array $filters
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getReviews(array $filters, int $itemsPerPage, int $page): array
    {
        return $this->reviewRepositor->getAllByFilter($filters, $itemsPerPage, $page);
    }

    /**
     * @param int $id
     * @return Review
     */
    public function getReviewById(int $id): Review
    {
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!$review) {
            throw new NotFoundHttpException('Review not found');
        }

        return $review;
    }

    /**
     * @param array $data
     * @return Review
     * @throws \DateMalformedStringException
     */
    public function createReview(array $data): Review
    {
        $this->requestCheckerService::check($data, self::REQUIRED_REVIEW_CREATE_FIELDS);

        $review = new Review();

        return $this->objectHandlerService->saveEntity($review, $data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Review
     * @throws \DateMalformedStringException
     */
    public function updateReview(int $id, array $data): Review
    {
        $review = $this->getReviewById($id);

        return $this->objectHandlerService->saveEntity($review, $data);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteReview(int $id): void
    {
        $review = $this->getReviewById($id);

        $this->entityManager->remove($review);
        $this->entityManager->flush();
    }

}