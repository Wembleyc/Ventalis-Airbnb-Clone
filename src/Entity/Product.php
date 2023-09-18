<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column(type: "integer")]
    private ?int $stock = 1;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $valide = null;
    
    #[ORM\OneToMany(targetEntity: PanierProduct::class, mappedBy: 'product')]
    private Collection $panierProducts;

    public function __construct()
    {
        $this->panierProducts = new ArrayCollection();
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
            $panierProduct->setProduct($this);
        }

        return $this;
    }

    public function removePanierProduct(PanierProduct $panierProduct): self
    {
        if ($this->panierProducts->removeElement($panierProduct)) {
            if ($panierProduct->getProduct() === $this) {
                $panierProduct->setProduct(null);
            }
        }

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValide(): ?string
    {
        return $this->valide;
    }

    public function setValide(?string $valide): self
    {
        $this->valide = $valide;

        return $this;
    }
}
