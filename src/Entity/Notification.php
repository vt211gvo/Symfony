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
            normalizationContext: ['groups' => 'get:item:notification']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:notification']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:notification'],
            denormalizationContext: ['groups' => 'post:collection:notification']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:notification'],
            denormalizationContext: ['groups' => 'patch:item:notification']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Notification implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:notification', 'get:collection:notification'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[Groups([
        'get:item:notification',
        'get:collection:notification',
        'post:collection:notification',
        'patch:item:notification'
    ])]
    private ?Guest $guest = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Message cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([
        'get:item:notification',
        'get:collection:notification',
        'post:collection:notification',
        'patch:item:notification'
    ])]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\Type(
        type: '\DateTimeInterface',
        message: 'The value {{ value }} is not a valid datetime.'
    )]
    #[Groups([
        'get:item:notification',
        'get:collection:notification',
        'post:collection:notification',
        'patch:item:notification'
    ])]
    private ?\DateTimeInterface $sentAt = null;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'guestId' => $this->guest?->getId(),
            'message' => $this->getMessage(),
            'sentAt' => $this->getSentAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
