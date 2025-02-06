<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_avis = null;

    #[ORM\Column(length: 255)]
    private ?string $nameClient = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_stock = null;

    #[ORM\Column]
    private ?int $id_produit = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAvis(): ?int
    {
        return $this->id_avis;
    }

    public function setIdAvis(int $id_avis): static
    {
        $this->id_avis = $id_avis;

        return $this;
    }

    public function getNameClient(): ?string
    {
        return $this->nameClient;
    }

    public function setNameClient(string $nameClient): static
    {
        $this->nameClient = $nameClient;

        return $this;
    }

    public function getDateStock(): ?\DateTimeInterface
    {
        return $this->date_stock;
    }

    public function setDateStock(\DateTimeInterface $date_stock): static
    {
        $this->date_stock = $date_stock;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function setIdProduit(int $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
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
}
