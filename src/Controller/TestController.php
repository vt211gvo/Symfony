<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    private array $items = [
        ['id' => 1, 'name' => 'Item 1'],
        ['id' => 2, 'name' => 'Item 2'],
        ['id' => 3, 'name' => 'Item 3'],
    ];

    #[Route('/get', name: 'app_test', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $queryParams = $request->query->all();

        return new JsonResponse($queryParams, 200);
    }

    #[Route('/post', name: 'app_test_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        return new JsonResponse($requestBody);
    }

    #[Route('/get-items', name: 'app_test_get_items', methods: ['GET'])]
    public function getItems(Request $request): JsonResponse
    {
        return new JsonResponse($this->items, 200);
    }

    #[Route('/get-item/{id}', name: 'app_test_get_item', methods: ['GET'])]
    public function getItem(string $id): JsonResponse
    {
        $item = $this->findItemById($id);

        if ($item) {
            return new JsonResponse($item);
        }

        return new JsonResponse(['message' => 'Item not found'], 404);
    }

    #[Route('/delete-item/{id}', name: 'app_test_delete_item', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse
    {
        foreach ($this->items as $key => $item) {
            if ($item['id'] == $id) {
                unset($this->items[$key]);
                return new JsonResponse(['message' => 'Item deleted']);
            }
        }

        return new JsonResponse(['message' => 'Item not found'], 404);
    }

    private function findItemById(string $id): ?array
    {
        foreach ($this->items as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }

        return null;
    }
}
