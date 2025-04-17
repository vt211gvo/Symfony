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
            normalizationContext: ['groups' => 'get:item:service']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:service']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:service'],
            denormalizationContext: ['groups' => 'post:collection:service']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:service'],
            denormalizationContext: ['groups' => 'patch:item:service']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Service implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'get:item:service',
        'get:collection:service',
        'get:item:serviceOrder',
        'get:collection:serviceOrder'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The service name cannot be blank.")]
    #[Groups([
        'get:item:service',
        'get:collection:service',
        'post:collection:service',
        'patch:item:service',
        'get:item:serviceOrder',
        'get:collection:serviceOrder'
    ])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "The service price cannot be blank.")]
    #[Assert\Positive(message: "The price must be a positive number.")]
    #[Groups([
        'get:item:service',
        'get:collection:service',
        'post:collection:service',
        'patch:item:service'
    ])]
    private ?string $price = null;

    /**
     * @var Collection<int, ServiceOrder>
     */
    #[ORM\OneToMany(targetEntity: ServiceOrder::class, mappedBy: 'service')]
    #[Groups([
        'get:item:service',
    ])]
    private Collection $serviceOrders;

    public function __construct()
    {
        $this->serviceOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ServiceOrder>
     */
    public function getServiceOrders(): Collection
    {
        return $this->serviceOrders;
    }

    public function addServiceOrder(ServiceOrder $serviceOrder): static
    {
        if (!$this->serviceOrders->contains($serviceOrder)) {
            $this->serviceOrders->add($serviceOrder);
            $serviceOrder->setService($this);
        }

        return $this;
    }

    public function removeServiceOrder(ServiceOrder $serviceOrder): static
    {
        if ($this->serviceOrders->removeElement($serviceOrder)) {
            // set the owning side to null (unless already changed)
            if ($serviceOrder->getService() === $this) {
                $serviceOrder->setService(null);
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
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'ordersCount' => $this->serviceOrders->count(),
        ];
    }
}
