<?php

namespace App\Entity;

use App\Repository\InspectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InspectionRepository::class)]
class Inspection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date_inspection = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_inspection = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Le commentaire ne peut pas être vide.')]
    private ?string $resultat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La note ne peut pas être vide.')]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: 'La note doit être entre 0 et 10.')]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'inspections')]
    private ?Avis $avis = null;


    public function __construct()
    {
        $this->date_inspection = new \DateTimeImmutable(); // Définit la date et l'heure actuelles automatiquement
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getDateInspection(): ?\DateTimeImmutable
    {
        return $this->date_inspection;
    }

    public function setDateInspection(\DateTimeImmutable $date_inspection): static
    {
        $this->date_inspection = $date_inspection;
        return $this;
    }

    public function getTypeInspection(): ?string
    {
        return $this->type_inspection;
    }

    public function setTypeInspection(string $type_inspection): static
    {
        $this->type_inspection = $type_inspection;
        return $this;
    }



    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): static
    {
        $this->resultat = $resultat;
        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getAvis(): ?Avis
    {
        return $this->avis;
    }

    public function setAvis(?Avis $avis): static
    {
        $this->avis = $avis;

        return $this;
    }

}
