<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StockRepository::class)]
#[UniqueEntity('nom', message: 'Ce stock existe déjà.')]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du stock ne peut pas être vide.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Le nom du stock ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $nom = null;


    #[Assert\NotBlank(message: "La date du produit ne peut pas être vide.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date doit être au bon format.")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "le produit ne peux pas prendre une date déja passée"
    )]
        #[ORM\Column(type: Types::DATE_MUTABLE)]
        private ?\DateTimeInterface $date_stock = null;


        #[Assert\NotNull(message: 'La date d\'expiration est requise.')]
        #[Assert\Type(type: '\DateTimeInterface', message: 'La date d\'expiration doit être une date valide.')]
        #[Assert\GreaterThan(propertyPath: 'date_stock', message: 'La date d\'expiration doit être après la date de stock.')]
            #[ORM\Column(type: Types::DATE_MUTABLE)]
            private ?\DateTimeInterface $date_expiration = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le lieu du stock ne peut pas être vide.')]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'Le nom du stock ne peut pas être vide.')]
    #[Assert\Choice(choices: ['Sec', 'Réfrigéré', 'Congelé'], message: 'La condition doit être soit "Sec", "Réfrigéré" ou "Congelé".')]
    private ?string $conditionn = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'Le nom du stock ne peut pas être vide.')]
    #[Assert\Choice(choices: ['Disponible', 'En rupture', 'Périmé'], message: 'Le statut doit être soit "Disponible", "En rupture" ou "Périmé".')]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: Produit::class, cascade: ['remove'])]
    private Collection $produits;
    
    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'stock')]
    private Collection $dechet;
    
    #[ORM\OneToMany(targetEntity: CultureAgricole::class, mappedBy: 'stock')]
    private Collection $recolte;
    
    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'stock_id')]
    private Collection $dechets;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'image doit être une URL valide.')]
    private ?string $image = null;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->dechet = new ArrayCollection();
        $this->recolte = new ArrayCollection();
        $this->dechets = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDateStock(): ?\DateTimeInterface { return $this->date_stock; }
    public function setDateStock(\DateTimeInterface $date_stock): self { $this->date_stock = $date_stock; return $this; }
    public function getDateExpiration(): ?\DateTimeInterface { return $this->date_expiration; }
    public function setDateExpiration(\DateTimeInterface $date_expiration): self { $this->date_expiration = $date_expiration; return $this; }
    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(string $lieu): self { $this->lieu = $lieu; return $this; }
    public function getConditionn(): ?string { return $this->conditionn; }
    public function setConditionn(string $conditionn): self { $this->conditionn = $conditionn; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
}
