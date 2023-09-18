<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Logements;
use App\Validator\MinimumOneDay;
use App\Validator\MinimumOneGuest;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Booking
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[MinimumOneDay]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    #[MinimumOneGuest]
    private ?int $guest_count = null;

    #[ORM\ManyToOne(inversedBy: 'reservation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Logements $logement = null;

    #[ORM\Column(length: 255)]
    private ?string $status_booking = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_reservation = null;

    private $totalPrice;


    #[ORM\PrePersist()]
    #[ORM\PreUpdate()]
    public function calculateTotalPrice(): void
    {
        if ($this->logement && $this->start_date && $this->end_date) {
            $priceByNight = $this->logement->getPriceByNight();  // Assuming getPriceByNight() exists in your Logement entity.
            $startDate = $this->getStartDate(); // Assuming getStartDate() exists in your Booking entity.
            $endDate = $this->getEndDate(); // Assuming getEndDate() exists in your Booking entity.
            $numberOfNights = $endDate->diff($startDate)->days;

            $this->totalPrice = $priceByNight * $numberOfNights;
        } else {
            $this->totalPrice = 0.0;
        }
    }
    public function getPriceByNight(): ?float
    {
        if ($this->logement) {
            return $this->logement->getPriceByNight();  // Supposons que vous avez une méthode getPriceByNight() dans votre entité Logement.
        }

        return null;
    }
    public function getNumberOfNights(): int
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        if ($startDate && $endDate) {
            $interval = $endDate->diff($startDate);
            return $interval->days;
        }
        return 0;
    }

    public function getTotalPrice(): float
    {
        // Ici, la logique pour calculer le prix total
        return $this->getLogement()->getPriceByNight() * $this->getNumberOfNights();
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }


    public function __construct()
    {
        $this->status_booking = Booking::STATUS_PENDING; // Par défaut, "En attente de confirmation"
    }

    public function getUserFullName()
    {
        if ($this->getUserReservation()) {
            return $this->getUserReservation()->getFirstName() . ' ' . $this->getUserReservation()->getLastName();
        }

        return 'N/A';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;
        return $this;
    }

    public function getGuestCount(): ?int
    {
        return $this->guest_count;
    }

    public function setGuestCount(int $guest_count): self
    {
        $this->guest_count = $guest_count;
        return $this;
    }

    public function getLogement(): ?Logements
    {
        return $this->logement;
    }

    public function setLogement(?Logements $logement): self
    {
        $this->logement = $logement;
        return $this;
    }


    public function getStatusBooking(): ?string
    {
        return $this->status_booking;
    }

    public function setStatusBooking(string $status_booking): self
    {
        $this->status_booking = $status_booking;
        return $this;
    }

    // ... autres méthodes utiles ...

    public function getUserReservation(): ?user
    {
        return $this->user_reservation;
    }

    public function setUserReservation(?user $user_reservation): static
    {
        $this->user_reservation = $user_reservation;

        return $this;
    }
}
