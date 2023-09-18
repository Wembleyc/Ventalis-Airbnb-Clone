<?php

namespace App\Entity;

use App\Repository\BackOfficeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BackOfficeRepository::class)]
class BackOffice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Users = null;

    #[ORM\Column(length: 255)]
    private ?string $Logements = null;

    #[ORM\Column(length: 255)]
    private ?string $Bookings = null;

    #[ORM\Column(length: 255)]
    private ?string $Avis = null;

    #[ORM\Column(length: 500)]
    private ?string $Messages = null;

    #[ORM\Column(length: 255)]
    private ?string $Paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $logement_status = null;

    #[ORM\Column(length: 255)]
    private ?string $Calendrier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?string
    {
        return $this->Users;
    }

    public function setUsers(string $Users): static
    {
        $this->Users = $Users;

        return $this;
    }

    public function getLogements(): ?string
    {
        return $this->Logements;
    }

    public function setLogements(string $Logements): static
    {
        $this->Logements = $Logements;

        return $this;
    }

    public function getBookings(): ?string
    {
        return $this->Bookings;
    }

    public function setBookings(string $Bookings): static
    {
        $this->Bookings = $Bookings;

        return $this;
    }

    public function getAvis(): ?string
    {
        return $this->Avis;
    }

    public function setAvis(string $Avis): static
    {
        $this->Avis = $Avis;

        return $this;
    }

    public function getMessages(): ?string
    {
        return $this->Messages;
    }

    public function setMessages(string $Messages): static
    {
        $this->Messages = $Messages;

        return $this;
    }

    public function getPaiement(): ?string
    {
        return $this->Paiement;
    }

    public function setPaiement(string $Paiement): static
    {
        $this->Paiement = $Paiement;

        return $this;
    }

    public function getLogementStatus(): ?string
    {
        return $this->logement_status;
    }

    public function setLogementStatus(string $logement_status): static
    {
        $this->logement_status = $logement_status;

        return $this;
    }

    public function getCalendrier(): ?string
    {
        return $this->Calendrier;
    }

    public function setCalendrier(string $Calendrier): static
    {
        $this->Calendrier = $Calendrier;

        return $this;
    }
}
