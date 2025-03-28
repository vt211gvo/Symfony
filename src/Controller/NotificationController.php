<?php

namespace App\Controller;

use App\Services\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * NotificationController handles notification-related operations.
 */
#[Route('/notification', name: 'notification_routes')]
class NotificationController extends AbstractController
{
    /**
     * @var NotificationService
     */
    private NotificationService $notificationService;

    /**
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @return JsonResponse
     */
    #[Route('/', name: 'get_notifications', methods: ['GET'])]
    public function getNotifications(): JsonResponse
    {
        $notifications = $this->notificationService->getNotifications();
        return new JsonResponse($notifications, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'get_notification', methods: ['GET'])]
    public function getNotification(int $id): JsonResponse
    {
        $notification = $this->notificationService->getNotificationById($id);
        return new JsonResponse($notification, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/', name: 'create_notification', methods: ['POST'])]
    public function createNotification(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $notification = $this->notificationService->createNotification($requestData);
        return new JsonResponse($notification, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    #[Route('/{id}', name: 'update_notification', methods: ['PATCH'])]
    public function updateNotification(Request $request, int $id): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $notification = $this->notificationService->updateNotification($id, $requestData);
        return new JsonResponse($notification, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete_notification', methods: ['DELETE'])]
    public function deleteNotification(int $id): JsonResponse
    {
        $this->notificationService->deleteNotification($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
