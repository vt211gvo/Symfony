<?php

namespace App\Services;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    public const REQUIRED_PAYMENT_CREATE_FIELDS = [
        'booking',
        'amount',
        'paymentMethod',
        'status',
    ];

    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;
    private ObjectHandlerService $objectHandlerService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param ObjectHandlerService $objectHandlerService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService,
        ObjectHandlerService $objectHandlerService
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
        $this->objectHandlerService = $objectHandlerService;
    }

    /**
     * @return array
     */
    public function getPayments(): array
    {
        return $this->entityManager->getRepository(Payment::class)->findAll();
    }


    /**
     * @param int $id
     * @return Payment
     */
    public function getPaymentById(int $id): Payment
    {
        $payment = $this->entityManager->getRepository(Payment::class)->find($id);

        if (!$payment) {
            throw new NotFoundHttpException('Payment not found');
        }

        return $payment;
    }


    /**
     * @param array $data
     * @return Payment
     * @throws \DateMalformedStringException
     */
    public function createPayment(array $data): Payment
    {
        $this->requestCheckerService::check($data, self::REQUIRED_PAYMENT_CREATE_FIELDS);

        $payment = new Payment();

        return $this->objectHandlerService->saveEntity($payment, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Payment
     * @throws \DateMalformedStringException
     */
    public function updatePayment(int $id, array $data): Payment
    {
        $payment = $this->getPaymentById($id);

        return $this->objectHandlerService->saveEntity($payment, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deletePayment(int $id): void
    {
        $payment = $this->getPaymentById($id);

        $this->entityManager->remove($payment);
        $this->entityManager->flush();
    }
}