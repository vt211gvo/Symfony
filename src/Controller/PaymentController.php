<?php

namespace App\Controller;

use App\Services\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_payments', methods: ['GET'])]
    public function getPayments(): JsonResponse
    {
        $payments = $this->paymentService->getPayments();
        return new JsonResponse($payments, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
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
    #[Route('/', name: 'create_payment', methods: ['POST'])]
    public function createPayment(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $payment = $this->paymentService->createPayment($requestData);
        return new JsonResponse($payment, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_payment', methods: ['PATCH'])]
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $payment = $this->paymentService->updatePayment($id, $requestData);
        return new JsonResponse($payment, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_payment', methods: ['DELETE'])]
    public function deletePayment(int $id): JsonResponse
    {
        $this->paymentService->deletePayment($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
