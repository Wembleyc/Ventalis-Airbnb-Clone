<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'panier', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\OneToMany(targetEntity: PanierProduct::class, mappedBy: 'panier')]
    private Collection $panierProducts;
    private int $totalPrice;

    public function __construct()
    {
        $this->panierProducts = new ArrayCollection();
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(User $User): static
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, PanierProduct>
     */
    public function getPanierProducts(): Collection
    {
        return $this->panierProducts;
    }

    public function addPanierProduct(PanierProduct $panierProduct): self
    {
        if (!$this->panierProducts->contains($panierProduct)) {
            $this->panierProducts[] = $panierProduct;
            $panierProduct->setPanier($this);
        }

        return $this;
    }

    public function removePanierProduct(PanierProduct $panierProduct): self
    {
        if ($this->panierProducts->removeElement($panierProduct)) {
            if ($panierProduct->getPanier() === $this) {
                $panierProduct->setPanier(null);
            }
        }

        return $this;
    }
}
