<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_stock = null;

    #[ORM\Column]
    private ?float $quantite_initiale = null;

    #[ORM\Column]
    private ?float $quantite_utilise = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_expiration = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $conditionn = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getQuantiteInitiale(): ?float
    {
        return $this->quantite_initiale;
    }

    public function setQuantiteInitiale(float $quantite_initiale): static
    {
        $this->quantite_initiale = $quantite_initiale;

        return $this;
    }

    public function getQuantiteUtilise(): ?float
    {
        return $this->quantite_utilise;
    }

    public function setQuantiteUtilise(float $quantite_utilise): static
    {
        $this->quantite_utilise = $quantite_utilise;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getConditionn(): ?string
    {
        return $this->conditionn;
    }

    public function setConditionn(string $conditionn): static
    {
        $this->conditionn = $conditionn;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }
}
