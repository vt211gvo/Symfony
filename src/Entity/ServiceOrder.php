<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:serviceOrder']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:serviceOrder']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:serviceOrder'],
            denormalizationContext: ['groups' => 'post:collection:serviceOrder']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:serviceOrder'],
            denormalizationContext: ['groups' => 'patch:item:serviceOrder']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class ServiceOrder implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:serviceOrder', 'get:collection:serviceOrder'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'serviceOrders')]
    #[Assert\NotNull(message: 'Guest cannot be null')]
    #[Assert\Valid]
    #[Groups([
        'get:item:serviceOrder',
        'get:collection:serviceOrder',
        'post:collection:serviceOrder',
        'patch:item:serviceOrder'
    ])]
    private ?Guest $guest = null;

    #[ORM\ManyToOne(inversedBy: 'serviceOrders')]
    #[Assert\NotNull(message: 'Service cannot be null')]
    #[Assert\Valid]
    #[Groups([
        'get:item:serviceOrder',
        'get:collection:serviceOrder',
        'post:collection:serviceOrder',
        'patch:item:serviceOrder'
    ])]
    private ?Service $service = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'Order date cannot be null')]
    #[Assert\Date(message: 'Order date must be a valid date')]
    #[Assert\LessThanOrEqual("today", message: 'Order date must be today or in the past')]
    #[Groups([
        'get:item:serviceOrder',
        'get:collection:serviceOrder',
        'post:collection:serviceOrder',
        'patch:item:serviceOrder'
    ])]
    private ?\DateTimeInterface $orderDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    public function setGuest(?Guest $guest): static
    {
        $this->guest = $guest;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    /**
     * @param \DateTimeInterface $orderDate
     * @return $this
     */
    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'guest' => $this->guest?->getId(),
            'service' => $this->service?->getName(),
            'orderDate' => $this->orderDate?->format('Y-m-d H:i:s'),
        ];
    }
}
