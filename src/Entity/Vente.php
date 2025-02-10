<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
#[Assert\NotBlank(message: "La date est obligatoire.")]
#[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
private ?\DateTimeInterface $date = null;


    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le prix doit être un nombre.")]
    #[Assert\Positive(message: "Le prix doit être supérieur à 0.")]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La méthode de paiement est obligatoire.")]
    #[Assert\Choice(choices: ["espèces", "carte_bancaire", "chèque", "virement"], message: "Veuillez choisir une méthode de paiement valide.")]


    private ?string $methodepayement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getMethodepayement(): ?string
    {
        return $this->methodepayement;
    }

    public function setMethodepayement(string $methodepayement): static
    {
        $this->methodepayement = $methodepayement;

        return $this;
    }
}
