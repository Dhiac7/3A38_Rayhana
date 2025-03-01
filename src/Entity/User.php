<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert ;
use App\Repository\UserRepository;
use  Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Email deja utilisé.')]
#[UniqueEntity(fields: ['tel'], message: 'Numéro deja utilisé.')]
#[UniqueEntity(fields: ['cin'], message: 'Cin deja utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/",
        message: "Le nom ne peut contenir que des lettres, espaces, apostrophes ou tirets."
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
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/",
        message: "Le prénom ne peut contenir que des lettres, espaces, apostrophes ou tirets."
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
    private ?string $photo = null;


    //
    private static ?User $currentUser = null;

    // Define roles as constants
    public const ROLE_CLIENT = 'client';
    public const ROLE_FERMIER = 'fermier';
    public const ROLE_AGRICULTEUR = 'agriculteur';
    public const ROLE_INSPECTEUR = 'inspecteur';
    public const ROLE_LIVREUR = 'livreur';
    #[ORM\Column(type: Types::STRING, length: 100)]
    //#[Assert\NotBlank(message: "Le rôle est obligatoire.")]
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
    #[Assert\Regex(
        pattern: "/^(?=.*[a-zA-Z])(?=.*\d).+$/",
        message: "Le mot de passe doit contenir au moins une lettre et un chiffre."
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

    /**
     * @var Collection<int, Vente>
     */
    #[ORM\OneToMany(targetEntity: Vente::class, mappedBy: 'user')]
    private Collection $ventes;

    #[ORM\Column(nullable: true)]
    private ?float $nbrHeuresTravail = null;


    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'client')]
    private Collection $avis;

    /**
     * @var Collection<int, Transactionfinancier>
     */
    #[ORM\OneToMany(targetEntity: Transactionfinancier::class, mappedBy: 'User')]
    private Collection $transactionfinanciers;


    /**
     * @var Collection<int, Parcelle>
     */
    #[ORM\OneToMany(targetEntity: Parcelle::class, cascade: ['persist', 'remove'], mappedBy: 'id_user')]
    private Collection $parcelles;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'employes')]
    private ?self $agriculteur = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'agriculteur')]
    private Collection $employes;

    /**
     * @var Collection<int, CultureAgricole>
     */
    #[ORM\OneToMany(targetEntity: CultureAgricole::class, mappedBy: 'user')]
    private Collection $cultureAgricoles;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "Le genre est obligatoire.")]
    //#[Assert\Choice(choices: ["homme", "femme"], message: "Le genre doit être 'homme' ou 'femme'.")]
    private ?string $genre = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "L'année de naissance est obligatoire.")]
    #[Assert\Range(
        min: 1990,
        max: 2025,
        notInRangeMessage: "L'année de naissance doit être entre {{ min }} et {{ max }}."
    )]
    private ?int $AnneeNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    private ?self $createdBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'createdBy')]
    private Collection $users;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $GoogleId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $SessionId = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'recipient')]
    private Collection $receivedMessages;



    public function __construct()
    {
        $this->ateliers = new ArrayCollection();
        $this->ventes = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->transactionfinanciers = new ArrayCollection();
        $this->parcelles = new ArrayCollection();
        $this->employes = new ArrayCollection();
        $this->cultureAgricoles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function setCurrentUser(?User $user): void
    {
        self::$currentUser = $user;
    }

    public static function getCurrentUser(): ?User
    {
        return self::$currentUser;
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

////////////////////////////////////////

public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function setPassword(string $mdp): self
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role)];
    }
    

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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
            $vente->setUser($this);
        }

        return $this;
    }

    public function removeVente(Vente $vente): static
    {
        if ($this->ventes->removeElement($vente)) {
            // set the owning side to null (unless already changed)
            if ($vente->getUser() === $this) {
                $vente->setUser(null);
            }
        }

        return $this;
    }

    public function getNbrHeuresTravail(): ?float
    {
        return $this->nbrHeuresTravail;
    }

    public function setNbrHeuresTravail(?float $nbrHeuresTravail): static
    {
        $this->nbrHeuresTravail = $nbrHeuresTravail;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setClient($this);
        }
        return $this; // Add the return statement
    }

    /**
     * @return Collection<int, Transactionfinancier>
     */
    public function getTransactionfinanciers(): Collection
    {
        return $this->transactionfinanciers;
    }

    public function addTransactionfinancier(Transactionfinancier $transactionfinancier): static
    {
        if (!$this->transactionfinanciers->contains($transactionfinancier)) {
            $this->transactionfinanciers->add($transactionfinancier);
            $transactionfinancier->setUser($this);
            }

        return $this;
    }

    /**
     * @return Collection<int, Parcelle>
     */
    public function getParcelles(): Collection
    {
        return $this->parcelles;
    }

    public function addParcelle(Parcelle $parcelle): static
    {
        if (!$this->parcelles->contains($parcelle)) {
            $this->parcelles->add($parcelle);
            $parcelle->setIdUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) { 
            $avi->setClient(null); 
        }
        return $this;
    }

    public function getAgriculteur(): ?self
    {
        return $this->agriculteur;
    }

    public function setAgriculteur(?self $agriculteur): static
    {
        $this->agriculteur = $agriculteur;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(self $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setAgriculteur($this);


        }

        return $this;
    }

    
    public function removeTransactionfinancier(Transactionfinancier $transactionfinancier): static
    {
        if ($this->transactionfinanciers->removeElement($transactionfinancier)) {
            // set the owning side to null (unless already changed)
            if ($transactionfinancier->getUser() === $this) {
                $transactionfinancier->setUser(null);
            }
        }

        return $this;
    }
    public function removeParcelle(Parcelle $parcelle): static
    {
        if ($this->parcelles->removeElement($parcelle)) {
            // set the owning side to null (unless already changed)
            if ($parcelle->getUser() === $this) {
                $parcelle->setIdUser(null);
            }
        }

        return $this;
    }
    public function removeEmploye(self $employe): static
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
                $employe->setAgriculteur(null);
            }
        
        return $this;
    }

    /**
     * @return Collection<int, CultureAgricole>
     */
    public function getCultureAgricoles(): Collection
    {
        return $this->cultureAgricoles;
    }

    public function addCultureAgricole(CultureAgricole $cultureAgricole): static
    {
        if (!$this->cultureAgricoles->contains($cultureAgricole)) {
            $this->cultureAgricoles->add($cultureAgricole);
            $cultureAgricole->setUser($this);
        }

        return $this;
    }

    public function removeCultureAgricole(CultureAgricole $cultureAgricole): static
    {
        if ($this->cultureAgricoles->removeElement($cultureAgricole)) {
            // set the owning side to null (unless already changed)
            if ($cultureAgricole->getUser() === $this) {
                $cultureAgricole->setUser(null);
            }
        }

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAnneeNaissance(): ?int
    {
        return $this->AnneeNaissance;
    }

    public function setAnneeNaissance(?int $AnneeNaissance): static
    {
        $this->AnneeNaissance = $AnneeNaissance;

        return $this;
    }
    // Calculate the age based on the birth year
    public function getAge(): ?int
    {
        if ($this->AnneeNaissance) {
            $currentYear = (int) date("Y");
            return $currentYear - $this->AnneeNaissance;
        }
        return null;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCreatedBy() === $this) {
                $user->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->GoogleId;
    }

    public function setGoogleId(?string $GoogleId): static
    {
        $this->GoogleId = $GoogleId;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->SessionId;
    }

    public function setSessionId(?string $SessionId): static
    {
        $this->SessionId = $SessionId;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $receivedMessage): static
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages->add($receivedMessage);
            $receivedMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $receivedMessage): static
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getRecipient() === $this) {
                $receivedMessage->setRecipient(null);
            }
        }

        return $this;
    }
}