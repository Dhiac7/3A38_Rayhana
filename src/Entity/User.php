<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert ;
use App\Repository\UserRepository;
use Doctrine\DBAL\Query\Limit;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank(message: "Le CIN est obligatoire.")]
    #[Assert\Length(
        exactMessage: "Le CIN doit contenir exactement {{ limit }} caractères.",
        exactly: 8
    )]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        message: "Le CIN doit contenir uniquement des chiffres."
    )]
    private ?string $cin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: "L'URL de la photo n'est pas valide.")]
    private ?string $photo = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le rôle est obligatoire.")]
    #[Assert\Choice(
        choices: ['client', 'fermier', 'agriculteur', 'inspecteur', 'livreur'],
        message: "Le rôle sélectionné n'est pas valide."
    )]
    private ?string $role;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $mdp = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        message: "Le numéro de téléphone doit contenir uniquement des chiffres."
    )]
    #[Assert\Length(
        exactMessage: "Le numéro de téléphone doit contenir exactement {{ limit }} caractères.",
        exactly :8
    )]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Choice(
        choices: ['actif', 'inactif', 'banni'],
        message: "Le statut sélectionné n'est pas valide."
    )]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(
        type: 'float',
        message: "Le salaire doit être un nombre décimal."
    )]
    #[Assert\PositiveOrZero(message: "Le salaire ne peut pas être négatif.")]
    private ?float $salaire = null;

    /**
     * @var Collection<int, Atelier>
     */
    #[ORM\ManyToMany(targetEntity: Atelier::class, mappedBy: 'users')]
    private Collection $ateliers;

    public function __construct()
    {
        $this->ateliers = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getPhoto(): ?string
    {
      /*  if ($this->photo && !str_starts_with($this->photo, 'http')) {
            return 'https://yourwebsite.com/uploads/photos/' . $this->photo;
        }*/
        return $this->photo;    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    
    public function setRole(string $role): static
    {
        if(!in_array($role,['client','fermier','agriculteur','inspecteur','livreur'])){
            throw new \InvalidArgumentException("erreur");
        }
        $this->role = $role;
        return $this;
    }
    
    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        if(!in_array($statut,['actif','inactif','banni'])){
            throw new \InvalidArgumentException("erreur");
        }
        $this->statut = $statut;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): static
    {
        $this->salaire = $salaire;

        return $this;
    }

    /**
     * @return Collection<int, Atelier>
     */
    public function getAteliers(): Collection
    {
        return $this->ateliers;
    }

    public function addAtelier(Atelier $atelier): static
    {
        if (!$this->ateliers->contains($atelier)) {
            $this->ateliers->add($atelier);
            $atelier->addUser($this);
        }

        return $this;
    }

    public function removeAtelier(Atelier $atelier): static
    {
        if ($this->ateliers->removeElement($atelier)) {
            $atelier->removeUser($this);
        }

        return $this;
    }

}
