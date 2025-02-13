<?php

namespace App\Entity;

use App\Repository\TransactionfinancierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionfinancierRepository::class)] // Correct repository
#[ORM\HasLifecycleCallbacks] // Enable lifecycle callbacks
class Transactionfinancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Define the "datetransaction" property if you want it
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetransaction = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le montant est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le montant doit être un nombre.")]
    #[Assert\Positive(message: "Le montant doit être supérieur à 0.")]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ["Dépense", "Revenu"], message: "Veuillez choisir un type valide.")]
    private ?string $type = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: "Le nombre d'heures est obligatoire.")]
    #[Assert\Positive(message: "Le nombre d'heures doit être supérieur à 0.")]
    private ?int $nbrheure = null;

    // Add lifecycle callback for default date setting
    #[ORM\PrePersist]
    public function setDefaultDate(): void
    {
        if ($this->datetransaction === null) {
            $this->datetransaction = new \DateTime(); // Set current date if none is provided
        }
    }

    // Getters and Setters for all fields

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetransaction(): ?\DateTimeInterface
    {
        return $this->datetransaction;
    }

    public function setDatetransaction(?\DateTimeInterface $datetransaction): static
    {
        $this->datetransaction = $datetransaction;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getNbrheure(): ?int
    {
        return $this->nbrheure;
    }

    public function setNbrheure(int $nbrheure): static
    {
        $this->nbrheure = $nbrheure;
        return $this;
    }
}
