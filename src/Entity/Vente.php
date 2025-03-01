<?php

// src/Entity/Vente.php
namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
#[ORM\HasLifecycleCallbacks] // Active les callbacks de cycle de vie
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null; // La date sera définie automatiquement

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le prix doit être un nombre.")]
    #[Assert\Positive(message: "Le prix doit être supérieur à 0.")]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La méthode de paiement est obligatoire.")]
    #[Assert\Choice(choices: ["espèces", "carte_bancaire", "chèque", "virement"], message: "Veuillez choisir une méthode de paiement valide.")]
    private ?string $methodepayement = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ventes')]
#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
private ?User $user = null;
    #[ORM\OneToOne(targetEntity: Transactionfinancier::class, mappedBy: 'vente', cascade: ['persist'])]
private ?Transactionfinancier $transaction = null;

#[ORM\ManyToOne(inversedBy: 'ventes')]
#[ORM\JoinColumn(name: "produit_id", referencedColumnName: "id", onDelete: "SET NULL")]
private ?Produit $produit = null;


    // Ajoutez cette méthode pour définir la date automatiquement
    #[ORM\PrePersist]
    public function setDateAutomatically(): void
    {
        $this->date = new \DateTime(); // Définit la date actuelle
    }

    // Quantité associée à la vente
    #[ORM\Column(type: Types::FLOAT)]
    private ?float $quantite = null;

    // Getters and Setters for the new 'quantite' property
    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }
    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    // Autres champs...

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    // Getters and Setters for other properties
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    #[ORM\PrePersist]
    public function setDefaultDate(): void
    {
        if ($this->date === null) {
            $this->date = new \DateTime(); // Si aucune date n'est définie, on utilise la date actuelle
        }
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

    public function getUser(): ?User
{
    return $this->user;
}

public function setUser(?User $user): self
{
    $this->user = $user;
    return $this;
}

    public function getTransaction(): ?Transactionfinancier
    {
        return $this->transaction;
    }

    public function setTransaction(?Transactionfinancier $transaction): static
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }
}
