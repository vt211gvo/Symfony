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
            normalizationContext: ['groups' => 'get:item:payment']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:payment']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:payment'],
            denormalizationContext: ['groups' => 'post:collection:payment']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:payment'],
            denormalizationContext: ['groups' => 'patch:item:payment']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Payment implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:payment', 'get:collection:payment'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[Assert\NotNull(message: 'Booking is required.')]
    #[Groups([
        'get:item:payment',
        'get:collection:payment',
        'post:collection:payment',
        'patch:item:payment'
    ])]
    private ?Booking $booking = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\NotNull(message: 'Amount is required.')]
    #[Assert\Positive(message: 'Amount must be a positive number.')]
    #[Groups([
        'get:item:payment',
        'get:collection:payment',
        'post:collection:payment',
        'patch:item:payment'
    ])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'Payment date is required.')]
    #[Assert\Type(type: '\DateTimeInterface', message: 'Invalid date format.')]
    #[Groups([
        'get:item:payment',
        'get:collection:payment',
        'post:collection:payment',
        'patch:item:payment'
    ])]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Payment method is required.')]
    #[Assert\Choice(choices: ['cash', 'credit_card', 'bank_transfer', 'paypal'], message: 'Invalid payment method.')]
    #[Groups([
        'get:item:payment',
        'get:collection:payment',
        'post:collection:payment',
        'patch:item:payment'
    ])]
    private ?string $method = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'bookingId' => $this->booking?->getId(),
            'amount' => $this->getAmount(),
            'paymentDate' => $this->paymentDate?->format('Y-m-d H:i:s'),
            'method' => $this->getMethod(),
        ];
    }
}
