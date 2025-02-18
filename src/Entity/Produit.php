<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('nom', message: 'Ce produit existe déjà .')]
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column]
    private ?float $prix_vente = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?float $quantite_vendues = null;

    #[ORM\Column]
    private ?bool $enPromotion = null;

    #[ORM\Column(nullable: true)]  // pourcentage_promo nullable
    #[Assert\Range(min: 0, max: 100, groups: ['Default'])]
    private ?int $pourcentage_promo = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_debut_promo = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_fin_promo = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $quantite_retourne = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_retour = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['Disponible', 'En rupture'], message: "Le statut doit être 'Disponible' ou 'En rupture'.")]
    private ?string $statut = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['Erreur de livraison', 'Produit endommagé'], message: "La raison doit être 'Erreur de livraison' ou 'Produit endommagé'.")]
    private ?string $raison_retour = null;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'stock_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Stock $stock = null;

    /**
     * @var Collection<int, Vente>
     */
    #[ORM\OneToMany(targetEntity: Vente::class, mappedBy: 'produit')]
    private Collection $ventes;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;  // Attribut nom restauré

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $description_globale = null;

    #[ORM\Column(length: 255)]
    private ?string $description_detaille = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    public function __construct()
    {
        $this->ventes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrixVente(): ?float
    {
        return $this->prix_vente;
    }

    public function setPrixVente(float $prix_vente): static
    {
        $this->prix_vente = $prix_vente;

        return $this;
    }

    public function getQuantiteVendues(): ?float
    {
        return $this->quantite_vendues;
    }

    public function setQuantiteVendues(?float $quantite_vendues): static
    {
        $this->quantite_vendues = $quantite_vendues;

        return $this;
    }

    public function isEnPromotion(): ?bool
    {
        return $this->enPromotion;
    }

    public function setEnPromotion(bool $enPromotion): static
    {
        $this->enPromotion = $enPromotion;

        return $this;
    }

    public function getPourcentagePromo(): ?int
    {
        return $this->pourcentage_promo;
    }

    public function setPourcentagePromo(?int $pourcentage_promo): static
    {
        $this->pourcentage_promo = $pourcentage_promo;
        return $this;
    }

    public function getDateDebutPromo(): ?\DateTimeInterface
    {
        return $this->date_debut_promo;
    }

    public function setDateDebutPromo(?\DateTimeInterface $date_debut_promo): static
    {
        $this->date_debut_promo = $date_debut_promo;
        return $this;
    }

    public function getDateFinPromo(): ?\DateTimeInterface
    {
        return $this->date_fin_promo;
    }

    public function setDateFinPromo(?\DateTimeInterface $date_fin_promo): static
    {
        $this->date_fin_promo = $date_fin_promo;
        return $this;
    }

    public function getQuantiteRetourne(): ?float
    {
        return $this->quantite_retourne;
    }

    public function setQuantiteRetourne(?float $quantite_retourne): static
    {
        $this->quantite_retourne = $quantite_retourne;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeInterface
    {
        return $this->date_retour;
    }

    public function setDateRetour(?\DateTimeInterface $date_retour): static
    {
        $this->date_retour = $date_retour;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        if (!in_array($statut, ['Disponible', 'En rupture'])) {
            throw new \InvalidArgumentException("Le statut doit être 'Disponible' ou 'En rupture'.");
        }

        $this->statut = $statut;
        return $this;
    }

    public function getRaisonRetour(): ?string
    {
        return $this->raison_retour;
    }

    public function setRaisonRetour(?string $raison_retour): self
    {
        if ($raison_retour && !in_array($raison_retour, ['Erreur de livraison', 'Produit endommagé'])) {
            throw new \InvalidArgumentException("La raison doit être 'Erreur de livraison' ou 'Produit endommagé'.");
        }

        $this->raison_retour = $raison_retour;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescriptionGlobale(): ?string
    {
        return $this->description_globale;
    }

    public function setDescriptionGlobale(string $description_globale): static
    {
        $this->description_globale = $description_globale;

        return $this;
    }

    public function getDescriptionDetaille(): ?string
    {
        return $this->description_detaille;
    }

    public function setDescriptionDetaille(string $description_detaille): static
    {
        $this->description_detaille = $description_detaille;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        if (!in_array($categorie, ['Fruits', 'Légumes', 'Déchets'])) {
            throw new \InvalidArgumentException("La catégorie doit être 'Fruits', 'Légumes', ou 'Déchets'.");
        }
        $this->categorie = $categorie;

        return $this;
    }
}
