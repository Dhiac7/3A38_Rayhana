<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $atelierid = null;

    #[ORM\Column]
    private ?int $idUser = null;
   
    #[Assert\NotBlank(message: "La date de l'atelier ne peut pas être vide.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date doit être au bon format.")]
    
    
        #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;

    #[Assert\NotBlank(message: "Tu dois choisir une option.")]//controle de saisie 
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\Choice(choices : ['enligne','carte','espece'], message: "Le mode de Paiement doit être 'enligne','carte','espece'.")]
    private ?string $modePaiement = null;

    #[Assert\NotBlank(message: "Tu dois choisir une option.")]//controle de saisie 
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\Choice(choices : ['confirmée', 'annulée', 'en attente'], message: "Le statut doit être 'confirmée', 'annulée', ou 'en attente'.")]
    private ?string $statut = null;


    #[Assert\Range(
        min: 1,  
        notInRangeMessage: "Le nombre de place doit être differente de 0"
    )]
    #[ORM\Column]
    private ?int $nbrPlace = null;

    #[Assert\NotBlank(message: "Tu dois choisir une option.")]//controle de saisie 
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Choice(choices : ['agriculteur','client','employee'],message: "le role doit étre 'agriculteur','client','employee' ")]
    private ?string $Role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAtelierid(): ?int
    {
        return $this->atelierid;
    }

    public function setAtelierid(int $atelierid): static
    {
        $this->atelierid = $atelierid;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): self
    {
        if(!in_array($modePaiement,['enligne','carte','espece'])){
            throw new \InvalidArgumentException("le mode de Paiement doit etre 'enligne','carte','espece'");
        }
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        if(!in_array($statut,['confirmée','annulée','en attente'])){
            throw new \InvalidArgumentException("le statut doit etre 'confirmée','annulée','en attente'");
        }
        $this->statut = $statut;
        return $this;
    }

    public function getNbrPlace(): ?int
    {
        return $this->nbrPlace;
    }

    public function setNbrPlace(int $nbrPlace): static
    {
        $this->nbrPlace = $nbrPlace;

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
}
