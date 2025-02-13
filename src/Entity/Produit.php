<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?float $quantite_vendues = null;

    #[ORM\Column]
    private ?bool $enPromotion = null;

    #[ORM\Column]
    private ?int $pourcentage_promo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut_promo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin_promo = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantite_retourne = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_retour = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_retour = null;

    #[ORM\ManyToOne(inversedBy: 'stock')]
    private ?Stock $stock = null;

    /**
     * @var Collection<int, Vente>
     */
    #[ORM\OneToMany(targetEntity: Vente::class, mappedBy: 'produit')]
    private Collection $ventes;

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

    public function setQuantiteVendues(float $quantite_vendues): static
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

    public function setPourcentagePromo(int $pourcentage_promo): static
    {
        $this->pourcentage_promo = $pourcentage_promo;

        return $this;
    }

    public function getDateDebutPromo(): ?\DateTimeInterface
    {
        return $this->date_debut_promo;
    }

    public function setDateDebutPromo(\DateTimeInterface $date_debut_promo): static
    {
        $this->date_debut_promo = $date_debut_promo;

        return $this;
    }

    public function getDateFinPromo(): ?\DateTimeInterface
    {
        return $this->date_fin_promo;
    }

    public function setDateFinPromo(\DateTimeInterface $date_fin_promo): static
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

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getRaisonRetour(): ?string
    {
        return $this->raison_retour;
    }

    public function setRaisonRetour(string $raison_retour): static
    {
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

    /**
     * @return Collection<int, Vente>
     */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }

    public function addVente(Vente $vente): static
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes->add($vente);
            $vente->setProduit($this);
        }

        return $this;
    }

    public function removeVente(Vente $vente): static
    {
        if ($this->ventes->removeElement($vente)) {
            // set the owning side to null (unless already changed)
            if ($vente->getProduit() === $this) {
                $vente->setProduit(null);
            }
        }

        return $this;
    }
}
