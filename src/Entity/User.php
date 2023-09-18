<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EventSubscriberInterface
{

#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;



    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 10, nullable: true)]
    protected ?int $employee_matricule = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_profil = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $conseiller = null;

    #[ORM\OneToMany(mappedBy: 'user_reservation', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;



    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }


    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }


    public function getEmployeeMatricule(): ?string
    {
        return $this->employee_matricule;
    }

    public function setEmployeeMatricule(int $employee_matricule): self
    {
        $this->employee_matricule = $employee_matricule;
        return $this;
    }


    public function getRolesWithLabels(): array
    {
        $roleLabels = [
            'ROLE_ADMIN' => 'Admin',
            'ROLE_USER' => 'Utilisateur',
            'ROLE_HOTE' => 'Hote',
            'ROLE_EMPLOYE' => 'Employé(e)',
        ];

        return array_map(function ($role) use ($roleLabels) {
            return $roleLabels[$role] ?? $role;
        }, $this->roles);
    }
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['hashPassword'],
        ];
    }

    public function hashPassword(UserPasswordHasherInterface $passwordHasher, BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof self)) {
            return;
        }

        $entity->setPassword(
            $passwordHasher->hashPassword($entity, $entity->getPassword())
        );

    }

    


    public function getImageProfil(): ?string
    {
        return $this->image_profil;
    }

    public function setImageProfil(?string $image_profil): static
    {
        $this->image_profil = $image_profil;

        return $this;
    }

    public function getConseiller(): ?user
    {
        return $this->conseiller;
    }

    public function setConseiller(?user $conseiller): static
    {
        $this->conseiller = $conseiller;

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
            $booking->setUserReservation($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUserReservation() === $this) {
                $booking->setUserReservation(null);
            }
        }

        return $this;
    }



}