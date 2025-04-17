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
            normalizationContext: ['groups' => 'get:item:bed']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:bed']
        ),
        new Post(
            normalizationContext: ['groups' => 'get:item:bed'],
            denormalizationContext: ['groups' => 'post:collection:bed']
        ),
        new Patch(
            normalizationContext: ['groups' => 'get:item:bed'],
            denormalizationContext: ['groups' => 'patch:item:bed']
        ),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Bed implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:bed', 'get:collection:bed'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[Assert\NotNull(message: 'Room cannot be null.')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'get:item:bed',
        'get:collection:bed',
        'post:collection:bed',
        'patch:item:bed'
    ])]
    private ?Room $room = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Bed number cannot be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Bed number cannot be longer than {{ limit }} characters.'
    )]
    #[Groups([
        'get:item:bed',
        'get:collection:bed',
        'post:collection:bed',
        'patch:item:bed'
    ])]
    private ?string $bedNumber = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'bed')]
    #[Assert\All([
        new Assert\Type(type: Booking::class, message: 'Each booking must be a valid Booking object.')
    ])]
    #[Groups(['get:item:bed'])]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * @param Room|null $room
     * @return $this
     */
    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBedNumber(): ?string
    {
        return $this->bedNumber;
    }

    /**
     * @param string $bedNumber
     * @return $this
     */
    public function setBedNumber(string $bedNumber): static
    {
        $this->bedNumber = $bedNumber;

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
            $booking->setBed($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBed() === $this) {
                $booking->setBed(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'bedNumber' => $this->getBedNumber(),
            'room' => $this->getRoom()?->getId(),
        ];
    }
}
