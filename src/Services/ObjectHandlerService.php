<?php

namespace App\Services;

use DateMalformedStringException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 */
class ObjectHandlerService
{

    /**
     * @var RequestCheckerService
     */
    private RequestCheckerService $requestCheckerService;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    /**
     * @param RequestCheckerService $requestCheckerService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RequestCheckerService  $requestCheckerService,
        EntityManagerInterface $entityManager)
    {
        $this->requestCheckerService = $requestCheckerService;
        $this->entityManager = $entityManager;
    }


    /**
     * @param mixed $object
     * @param array $fields
     * @return mixed
     * @throws DateMalformedStringException
     */
    public function setObjectData(mixed $object, array $fields): mixed
    {
        foreach ($fields as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (str_contains(strtolower($key), 'date')) {
                $value = new DateTime($value);
            }

            if (!method_exists($object, $method)) {
                continue;
            }

            $object->$method($value);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($object);

        return $object;
    }


    /**
     * @param object $entity
     * @param array $data
     * @return object|mixed
     * @throws DateMalformedStringException
     */
    public function saveEntity(object $entity, array $data): object
    {
        $entity = $this->setObjectData($entity, $data);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}