<?php

namespace App\Entity;

use App\Repository\TransactionfinancierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionfinancierRepository::class)]
class Transactionfinancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le montant est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le montant doit être un nombre.")]
    #[Assert\Positive(message: "Le montant doit être supérieur à 0.")]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
#[Assert\NotBlank(message: "La description est obligatoire.")]
#[Assert\Type(type: "string", message: "La description doit être une chaîne de caractères.")]
#[Assert\Length(min: 1, max: 255, minMessage: "La description doit comporter au moins 1 caractère.", maxMessage: "La description ne peut pas dépasser 255 caractères.")]
#[Assert\Regex(pattern: "/^[a-zA-Z\s]+$/", message: "La description ne peut contenir que des lettres et des espaces.")]
private ?string $description = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
    private ?\DateTimeInterface $datetransaction = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    #[Assert\Choice(choices: ["Dépense", "Revenu"], message: "Le type doit être soit 'Dépense' soit 'Revenu'.")]
    private ?string $type = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le nombre d'heures est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le nombre d'heures doit être un nombre.")]
    #[Assert\PositiveOrZero(message: "Le nombre d'heures doit être positif.")]
    private ?float $nbrheure = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatetransaction(): ?\DateTimeInterface
    {
        return $this->datetransaction;
    }

    public function setDatetransaction(\DateTimeInterface $datetransaction): static
    {
        $this->datetransaction = $datetransaction;

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

    public function getNbrheure(): ?float
    {
        return $this->nbrheure;
    }

    public function setNbrheure(float $nbrheure): static
    {
        $this->nbrheure = $nbrheure;

        return $this;
    }
}
