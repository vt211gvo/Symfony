<?php

namespace App\Controller;

use App\Services\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * PaymentController handles payment-related operations.
 */
#[Route('/payment', name: 'payment_routes')]
class PaymentController extends AbstractController
{
    /**
     * @var PaymentService
     */
    private PaymentService $paymentService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param PaymentService $paymentService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PaymentService $paymentService, EntityManagerInterface $entityManager)
    {
        $this->paymentService = $paymentService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_payments', methods: ['GET'])]
    public function getPayments(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->paymentService->getPayments($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_payment', methods: ['GET'])]
    public function getPayment(int $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        return new JsonResponse($payment, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_payment', methods: ['POST'])]
    public function createPayment(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $payment = $this->paymentService->createPayment($requestData);
        $this->entityManager->flush();

        return new JsonResponse($payment, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_payment', methods: ['PATCH'])]
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $payment = $this->paymentService->updatePayment($id, $requestData);
        $this->entityManager->flush();

        return new JsonResponse($payment, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_payment', methods: ['DELETE'])]
    public function deletePayment(int $id): JsonResponse
    {
        $this->paymentService->deletePayment($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
