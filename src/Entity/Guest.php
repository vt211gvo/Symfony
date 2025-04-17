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
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:guest']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:guest']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:guest'],
            denormalizationContext: ['groups' => 'post:collection:guest']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:guest'],
            denormalizationContext: ['groups' => 'patch:item:guest']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Guest  implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:guest', 'get:collection:guest'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Document number cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([
        'get:item:guest',
        'get:collection:guest',
        'post:collection:guest',
        'patch:item:guest'
    ])]
    private ?string $documentNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Phone number cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]{7,15}$/',
        message: 'Phone number must be a valid international format.'
    )]
    #[Groups([
        'get:item:guest',
        'get:collection:guest',
        'post:collection:guest',
        'patch:item:guest'
    ])]
    private ?string $phone = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'guest')]
    #[Groups(['get:item:guest'])]
    private Collection $bookings;

    /**
     * @var Collection<int, ServiceOrder>
     */
    #[ORM\OneToMany(targetEntity: ServiceOrder::class, mappedBy: 'guest')]
    #[Groups(['get:item:guest'])]
    private Collection $serviceOrders;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'guest')]
    #[Groups(['get:item:guest'])]
    private Collection $reviews;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'guest')]
    #[Groups(['get:item:guest'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->serviceOrders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(string $documentNumber): static
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setGuest($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getGuest() === $this) {
                $booking->setGuest(null);
            }
        }

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
            $serviceOrder->setGuest($this);
        }

        return $this;
    }

    public function removeServiceOrder(ServiceOrder $serviceOrder): static
    {
        if ($this->serviceOrders->removeElement($serviceOrder)) {
            // set the owning side to null (unless already changed)
            if ($serviceOrder->getGuest() === $this) {
                $serviceOrder->setGuest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setGuest($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGuest() === $this) {
                $review->setGuest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setGuest($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getGuest() === $this) {
                $notification->setGuest(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'documentNumber' => $this->getDocumentNumber(),
            'phone' => $this->getPhone(),
            'bookings' => $this->getBookings()->map(fn($b) => $b->getId())->toArray(),
            'serviceOrders' => $this->getServiceOrders()->map(fn($s) => $s->getId())->toArray(),
            'reviews' => $this->getReviews()->map(fn($r) => $r->getId())->toArray(),
            'notifications' => $this->getNotifications()->map(fn($n) => $n->getId())->toArray(),
        ];
    }
}
