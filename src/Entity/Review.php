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
            normalizationContext: ['groups' => 'get:item:review']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:review']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:review'],
            denormalizationContext: ['groups' => 'post:collection:review']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:review'],
            denormalizationContext: ['groups' => 'patch:item:review']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Review implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:review', 'get:collection:review'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[Assert\NotNull(message: 'Guest is required.')]
    #[Groups([
        'get:item:review',
        'get:collection:review',
        'post:collection:review',
        'patch:item:review'
    ])]
    private ?Guest $guest = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Comment cannot be blank.')]
    #[Assert\Length(min: 10, max: 1000, minMessage: 'Comment must be at least {{ limit }} characters long.', maxMessage: 'Comment cannot exceed {{ limit }} characters.')]
    #[Groups([
        'get:item:review',
        'get:collection:review',
        'post:collection:review',
        'patch:item:review'
    ])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Rating is required.')]
    #[Assert\Range(notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}.', min: 1, max: 5)]
    #[Groups([
        'get:item:review',
        'get:collection:review',
        'post:collection:review',
        'patch:item:review'
    ])]
    private ?int $rating = null;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'guestId' => $this->guest?->getId(),
            'comment' => $this->getComment(),
            'rating' => $this->getRating(),
        ];
    }
}
