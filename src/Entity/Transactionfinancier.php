<?php

// src/Entity/Transactionfinancier.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\TransactionfinancierRepository; // Ensure this use statement is present

#[ORM\Entity(repositoryClass: TransactionfinancierRepository::class)] // Ensure this is correct
class Transactionfinancier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $montant = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $type = null;

    #[ORM\OneToOne(targetEntity: Vente::class, inversedBy: 'transaction', cascade: ['remove'])]
    #[ORM\JoinColumn(name: "vente_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Vente $vente = null;

    #[ORM\ManyToOne(inversedBy: 'transactionfinanciers')]
    private ?User $user = null;
    

    
    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    #[ORM\PrePersist]
    public function setDateAutomatically(): void
    {
        $this->date = new \DateTime(); // Set the current date and time
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): self
    {
        $this->vente = $vente;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->User = $user;

        return $this;
    }
   

}