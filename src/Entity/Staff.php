<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:staff']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:staff']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:staff'],
            denormalizationContext: ['groups' => 'post:collection:staff']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:staff'],
            denormalizationContext: ['groups' => 'patch:item:staff']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Staff implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:staff', 'get:collection:staff'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Role cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Role cannot be longer than {{ limit }} characters')]
    #[Groups([
        'get:item:staff',
        'get:collection:staff',
        'post:collection:staff',
        'patch:item:staff'
    ])]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Name cannot be longer than {{ limit }} characters')]
    #[Groups([
        'get:item:staff',
        'get:collection:staff',
        'post:collection:staff',
        'patch:item:staff'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Phone number cannot be blank')]
    #[Assert\Length(min: 10, max: 15, minMessage: 'Phone number must be at least {{ limit }} characters long', maxMessage: 'Phone number cannot be longer than {{ limit }} characters')]
    #[Assert\Regex(pattern: '/^\+?[0-9]*$/', message: 'Phone number must be a valid number')]
    #[Groups([
        'get:item:staff',
        'get:collection:staff',
        'post:collection:staff',
        'patch:item:staff'
    ])]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'role' => $this->getRole(),
            'name' => $this->getName(),
            'phone' => $this->getPhone(),
        ];
    }
}
