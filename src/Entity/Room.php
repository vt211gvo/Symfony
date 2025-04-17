<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:room']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:room']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:room'],
            denormalizationContext: ['groups' => 'post:collection:room']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:room'],
            denormalizationContext: ['groups' => 'patch:item:room']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Room implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:room', 'get:collection:room'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:room',
        'get:collection:room',
        'post:collection:room',
        'patch:item:room'
    ])]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:room',
        'get:collection:room',
        'post:collection:room',
        'patch:item:room'
    ])]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Groups([
        'get:item:room',
        'get:collection:room',
        'post:collection:room',
        'patch:item:room'
    ])]
    private ?int $capacity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\Positive]
    #[Groups([
        'get:item:room',
        'get:collection:room',
        'post:collection:room',
        'patch:item:room'
    ])]
    private ?string $price = null;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\OneToMany(targetEntity: Bed::class, mappedBy: 'room')]
    #[Groups(['get:item:room'])]
    private Collection $beds;

    public function __construct()
    {
        $this->beds = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return $this
     */
    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return $this
     */
    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Bed>
     */
    public function getBeds(): Collection
    {
        return $this->beds;
    }

    public function addBed(Bed $bed): static
    {
        if (!$this->beds->contains($bed)) {
            $this->beds->add($bed);
            $bed->setRoom($this);
        }

        return $this;
    }

    public function removeBed(Bed $bed): static
    {
        if ($this->beds->removeElement($bed)) {
            if ($bed->getRoom() === $this) {
                $bed->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'number' => $this->getNumber(),
            'type' => $this->getType(),
            'capacity' => $this->getCapacity(),
            'price' => $this->getPrice(),
            'bedCount' => $this->beds->count(),
        ];
    }
}
