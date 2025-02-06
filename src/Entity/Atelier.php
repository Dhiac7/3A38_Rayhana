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
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_atelier = null;

    #[ORM\Column]
    private ?int $capacite_max = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $idUser = 0;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['ouvert','complet','annulé'],message: "le statut doit étre 'ouvert','complet','annulé' ")]
    private ?string $statut = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices: ['agriculteur','client','employee'],message: "le role doit étre 'agriculteur','client','employee' ")]
    private ?string $Role = 'client';

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ateliers')]
    private Collection $users;

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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    { $idUser=0;
        $this->idUser = $idUser;

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
}
