<?php

namespace App\Services;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationService
{
    public const REQUIRED_NOTIFICATION_CREATE_FIELDS = [
        'user',
        'message',
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
    public function getNotifications(): array
    {
        return $this->entityManager->getRepository(Notification::class)->findAll();
    }

    /**
     * @param int $id
     * @return Notification
     */
    public function getNotificationById(int $id): Notification
    {
        $notification = $this->entityManager->getRepository(Notification::class)->find($id);

        if (!$notification) {
            throw new NotFoundHttpException('Notification not found');
        }

        return $notification;
    }


    /**
     * @param array $data
     * @return Notification
     * @throws \DateMalformedStringException
     */
    public function createNotification(array $data): Notification
    {
        $this->requestCheckerService::check($data, self::REQUIRED_NOTIFICATION_CREATE_FIELDS);

        $notification = new Notification();

        return $this->objectHandlerService->saveEntity($notification, $data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return Notification
     * @throws \DateMalformedStringException
     */
    public function updateNotification(int $id, array $data): Notification
    {
        $notification = $this->getNotificationById($id);

        return $this->objectHandlerService->saveEntity($notification, $data);
    }


    /**
     * @param int $id
     * @return void
     */
    public function deleteNotification(int $id): void
    {
        $notification = $this->getNotificationById($id);

        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }
}