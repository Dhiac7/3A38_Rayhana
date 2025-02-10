<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

   
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['ouvert','complet','annulé'],message: "le statut doit étre 'ouvert','complet','annulé' ")]
    private ?string $conditionn = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['ouvert','complet','annulé'],message: "le statut doit étre 'ouvert','complet','annulé' ")]
    private ?string $statut = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'stock')]
    private Collection $stock;

    /**
     * @var Collection<int, Dechet>
     */
    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'stock')]
    private Collection $dechet;

    /**
     * @var Collection<int, CultureAgricole>
     */
    #[ORM\OneToMany(targetEntity: CultureAgricole::class, mappedBy: 'stock')]
    private Collection $recolte;

    /**
     * @var Collection<int, Dechet>
     */
    #[ORM\OneToMany(targetEntity: Dechet::class, mappedBy: 'stock_id')]
    private Collection $dechets;

    public function __construct()
    {
        $this->stock = new ArrayCollection();
        $this->dechet = new ArrayCollection();
        $this->recolte = new ArrayCollection();
        $this->dechets = new ArrayCollection();
    }

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

    public function setConditionn(string $conditionn): self
    {
        if(!in_array($conditionn,['ouvert','complet','annulé'])){
            throw new \InvalidArgumentException("le statut doit etre 'ouvert','complet','annulé' ");
        }
        $this->conditionn = $conditionn;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        if(!in_array($statut,['ouvert','complet','annulé'])){
            throw new \InvalidArgumentException("le statut doit etre 'ouvert','complet','annulé' ");
        }

        $this->statut = $statut;
        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getStock(): Collection
    {
        return $this->stock;
    }

    public function addStock(Produit $stock): static
    {
        if (!$this->stock->contains($stock)) {
            $this->stock->add($stock);
            $stock->setStock($this);
        }

        return $this;
    }

    public function removeStock(Produit $stock): static
    {
        if ($this->stock->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getStock() === $this) {
                $stock->setStock(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dechet>
     */
    public function getDechet(): Collection
    {
        return $this->dechet;
    }

    public function addDechet(Dechet $dechet): static
    {
        if (!$this->dechet->contains($dechet)) {
            $this->dechet->add($dechet);
            $dechet->setStock($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechet->removeElement($dechet)) {
            // set the owning side to null (unless already changed)
            if ($dechet->getStock() === $this) {
                $dechet->setStock(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CultureAgricole>
     */
    public function getRecolte(): Collection
    {
        return $this->recolte;
    }

    public function addRecolte(CultureAgricole $recolte): static
    {
        if (!$this->recolte->contains($recolte)) {
            $this->recolte->add($recolte);
            $recolte->setStock($this);
        }

        return $this;
    }

    public function removeRecolte(CultureAgricole $recolte): static
    {
        if ($this->recolte->removeElement($recolte)) {
            // set the owning side to null (unless already changed)
            if ($recolte->getStock() === $this) {
                $recolte->setStock(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dechet>
     */
    public function getDechets(): Collection
    {
        return $this->dechets;
    }
}
