<?php

namespace App\Entity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use App\Repository\DechetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;

#[ORM\Entity(repositoryClass: DechetRepository::class)]
class Dechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Tu dois choisir un type.")]//controle de saisie 
    #[Assert\Choice(choices: ['organique','plastique','métalique','vegetale'],message: "le type de dechet doit étre 'organique','plastique','métalique','vegetale'")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,  
        notInRangeMessage: "La capacité doit être  differente de 0"
    )]
    private ?float $quantite = null;

//date controle 
    #[Assert\NotBlank(message: "La date de l'atelier ne peut pas être vide.")]//controle de saisie 

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de production ne peut pas être vide.")]
//#[Assert\Date(message: "La date de production doit être une date valide.")]

    private ?\DateTimeInterface $dateProduction = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date expiration ne peut pas être vide.")]
//#[Assert\Date(message: "La date expiration doit être une date valide.")]

    private ?\DateTimeInterface $date_expiration = null;

    #[ORM\ManyToOne(inversedBy: 'dechet')]
    private ?Stock $stock = null;

    #[ORM\ManyToOne(inversedBy: 'dechets')]
    private ?Stock $stock_id = null;
    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->dateProduction && $this->date_expiration) {
            if ($this->date_expiration <= $this->dateProduction) {
                $context->buildViolation("La date d'expiration doit être supérieure à la date de production.")
                    ->atPath('date_expiration')
                    ->addViolation();
            }
        }
    } 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if(!in_array($type,['organique','plastique','métalique','vegetale'])){
            throw new \InvalidArgumentException("le type doit etre 'organique','plastique','métalique','vegetale' ");
        }
        $this->type = $type;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateProduction(): ?\DateTimeInterface
    {
        return $this->dateProduction;
    }

    public function setDateProduction(\DateTimeInterface $dateProduction): static
    {
        $this->dateProduction = $dateProduction;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {if(!in_array($statut,['resycler','eliminer'])){
        throw new \InvalidArgumentException("le statut doit etre 'resycler','eliminer' ");
    }
        $this->statut = $statut;

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

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getStockId(): ?Stock
    {
        return $this->stock_id;
    }

    public function setStockId(?Stock $stock_id): static
    {
        $this->stock_id = $stock_id;

        return $this;
    }
}
