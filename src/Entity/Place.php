<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\OneToOne(mappedBy: 'place', cascade: ['persist', 'remove'])]
    private ?Reservation $Place = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable; // Utilise la valeur passée en paramètre
        return $this;
    }

    public function getPlace(): ?Reservation
    {
        return $this->Place;
    }

    public function setPlace(?Reservation $Place): static
    {
        // unset the owning side of the relation if necessary
        if ($Place === null && $this->Place !== null) {
            $this->Place->setPlace(null);
        }

        // set the owning side of the relation if necessary
        if ($Place !== null && $Place->getPlace() !== $this) {
            $Place->setPlace($this);
        }

        $this->Place = $Place;

        return $this;
    }
}