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
            normalizationContext: ['groups' => 'get:item:booking']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:booking']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:booking'],
            denormalizationContext: ['groups' => 'post:collection:booking']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:booking'],
            denormalizationContext: ['groups' => 'patch:item:booking']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Booking implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:booking', 'get:collection:booking'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:booking',
        'get:collection:booking',
        'post:collection:booking',
        'patch:item:booking'
    ])]
    private ?Guest $guest = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:booking',
        'get:collection:booking',
        'post:collection:booking',
        'patch:item:booking'
    ])]
    private ?Bed $bed = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Groups([
        'get:item:booking',
        'get:collection:booking',
        'post:collection:booking',
        'patch:item:booking'
    ])]
    private ?\DateTimeInterface $checkinDate = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: "checkinDate", message: "Checkout date must be later than check-in date.")]
    #[Groups([
        'get:item:booking',
        'get:collection:booking',
        'post:collection:booking',
        'patch:item:booking'
    ])]
    private ?\DateTimeInterface $checkoutDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Status cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([
        'get:item:booking',
        'get:collection:booking',
        'post:collection:booking',
        'patch:item:booking'
    ])]
    private ?string $status = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'booking')]
    #[Assert\All([
        new Assert\Type(type: Payment::class, message: 'Each payment must be a valid Payment object.')
    ])]
    #[Groups(['get:item:booking'])]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

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

    public function getBed(): ?Bed
    {
        return $this->bed;
    }

    public function setBed(?Bed $bed): static
    {
        $this->bed = $bed;

        return $this;
    }

    public function getCheckinDate(): ?\DateTimeInterface
    {
        return $this->checkinDate;
    }

    public function setCheckinDate(\DateTimeInterface $checkinDate): static
    {
        $this->checkinDate = $checkinDate;

        return $this;
    }

    public function getCheckoutDate(): ?\DateTimeInterface
    {
        return $this->checkoutDate;
    }

    public function setCheckoutDate(\DateTimeInterface $checkoutDate): static
    {
        $this->checkoutDate = $checkoutDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setBooking($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            if ($payment->getBooking() === $this) {
                $payment->setBooking(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'guest' => $this->getGuest()?->getId(),
            'bed' => $this->getBed()?->getId(),
            'checkinDate' => $this->getCheckinDate()?->format('Y-m-d'),
            'checkoutDate' => $this->getCheckoutDate()?->format('Y-m-d'),
            'status' => $this->getStatus(),
            'payments' => $this->getPayments()->map(fn($p) => $p->getId())->toArray(),
        ];
    }
}
