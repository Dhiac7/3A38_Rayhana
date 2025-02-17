<?php

namespace App\Entity;

use App\Repository\ParcelleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;


#[ORM\Entity(repositoryClass: ParcelleRepository::class)]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la parcelle est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La superficie est obligatoire.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    private ?float $superficie = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La latitude est obligatoire.")]
    #[Assert\Range(
        min: -90,
        max: 90,
        notInRangeMessage: "La latitude doit être comprise entre -90 et 90."
    )]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La longitude est obligatoire.")]
    #[Assert\Range(
        min: -180,
        max: 180,
        notInRangeMessage: "La longitude doit être comprise entre -180 et 180."
    )]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Veuillez indiquer si l'irrigation est disponible.")]
    private ?string $irrigationDisponible = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'parcelles')]
    #[ORM\JoinColumn(nullable: true)] // Makes this relation mandatory
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getIrrigationDisponible(): ?string
    {
        return $this->irrigationDisponible;
    }

    public function setIrrigationDisponible(string $irrigationDisponible): static
    {
        $this->irrigationDisponible = $irrigationDisponible;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->user;
    }

    public function setIdUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
    
}
