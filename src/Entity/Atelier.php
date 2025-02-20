<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
#[ORM\Entity(repositoryClass: AtelierRepository::class)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom de l'atelier ne peut pas être vide.")]
    #[Assert\Length(
        max: 15, 
        maxMessage: "Le nom de l'atelier ne peut pas dépasser 30 caractères."
    )]
    #[Assert\Regex(
    pattern: "/^[a-zA-Z0-9À-ÿ\s]+$/",
    message: "Le nom de l'atelier doit contenir uniquement des lettres alphabétiques et des chiffres."
)]//controle de saisie 
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "La date de l'atelier ne peut pas être vide.")]
#[Assert\Type(type: \DateTimeInterface::class, message: "La date doit être au bon format.")]
#[Assert\GreaterThanOrEqual(
    value: "today",
    message: "l'atelier ne peux pas prendre une date déja passée"
)]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_atelier = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacite maximal de l'atelier ne peut pas être vide.")]
    #[Assert\Range(
        min: 1, 
        max: 500, 
        notInRangeMessage: "La capacité doit être comprise entre {{ min }} et {{ max }}."
    )]//controle de saisie 
    private ?int $capacite_max = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")] 
    #[Assert\Positive(message: "Le prix doit être strictement positif.")]
    private ?float $prix = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Tu dois choisir une option.")]

    #[Assert\Choice(choices: ['ouvert','complet','annulé'],message: "le statut doit étre 'ouvert','complet','annulé' ")]
    private ?string $statut = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Tu dois choisir une option.")] 

    #[Assert\Choice(choices: ['agriculteur','client','employee'],message: "le role doit étre 'agriculteur','client','employee' ")]
    private ?string $Role = 'client';


    #[ORM\Column (nullable:true)]
    private ?int $nbrplacedispo = null;

    /**
     * @var Collection<int, User>
     */




    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ateliers')]
    private Collection $users;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateAtelier(): ?\DateTimeInterface
    {
        return $this->date_atelier;
    }

    public function setDateAtelier(\DateTimeInterface $date_atelier): static
    {
        $this->date_atelier = $date_atelier;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capacite_max;
    }

    public function setCapaciteMax(int $capacite_max): static
    {
        $this->capacite_max = $capacite_max;

        return $this;
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

       public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {   $statut='ouvert';
        if(!in_array($statut,['ouvert','complet','annulé'])){
            throw new \InvalidArgumentException("le statut doit etre 'ouvert','complet','annulé' ");
        }
        $this->statut = $statut;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->Role;
    }

    public function setRole(string $Role): self
    {        if(!in_array($Role,['agriculteur','client','employee'])){
                throw new \InvalidArgumentException("le role doit etre 'agriculteur','client','employee'");
    }    
                $this->Role = $Role;
                return $this;

    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getNbrplacedispo(): ?int
    {
        return $this->nbrplacedispo;
    }

    public function setNbrplacedispo(int $nbrplacedispo): static
    {
        $this->nbrplacedispo = $nbrplacedispo;

        return $this;
    }
}