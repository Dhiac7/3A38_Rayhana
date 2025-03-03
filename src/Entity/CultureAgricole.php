<?php

namespace App\Entity;

use App\Repository\CultureAgricoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;




#[ORM\Entity(repositoryClass: CultureAgricoleRepository::class)]
class CultureAgricole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Le nom doit avoir au moins {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de culture est obligatoire.")]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date de semis doit être une date valide.")]
    private ?\DateTimeInterface $dateSemi = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La superficie est obligatoire.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    private ?float $superficie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?string $statut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le rendement estimé est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le rendement ne peut pas être négatif.")]
    private ?float $rendementEstime = null;

    #[ORM\ManyToOne(inversedBy: 'recolte')]
    private ?Stock $stock = null;

    #[ORM\ManyToOne(inversedBy: 'cultureAgricoles')]
    private ?User $user = null;

    /**
     * @var Collection<int, Parcelle>
     */
    #[ORM\ManyToMany(targetEntity: Parcelle::class, mappedBy: 'cultureAgricoles')]
    private Collection $parcelles;

    public function __construct()
    {
        $this->dateSemi = new \DateTime(); // Définit la date actuelle par défaut
        $this->parcelles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateSemi(): ?\DateTimeInterface
    {
        return $this->dateSemi;
    }

    public function setDateSemi(\DateTimeInterface $dateSemi): static
    {
        $this->dateSemi = $dateSemi;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

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

    public function getRendementEstime(): ?float
    {
        return $this->rendementEstime;
    }

    public function setRendementEstime(float $rendementEstime): static
    {
        $this->rendementEstime = $rendementEstime;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Parcelle>
     */
    public function getParcelles(): Collection
    {
        return $this->parcelles;
    }

    public function addParcelle(Parcelle $parcelle): self {
        if (!$this->parcelles->contains($parcelle)) {
            $this->parcelles[] = $parcelle;
            $parcelle->addCultureAgricole($this); // Mise à jour de l'autre côté
        }
        return $this;
    }
    
    public function removeParcelle(Parcelle $parcelle): self {
        if ($this->parcelles->removeElement($parcelle)) {
            $parcelle->removeCultureAgricole($this); // Mise à jour de l'autre côté
        }
        return $this;
    }
    



}
