<?php

namespace App\Entity;

use App\Repository\InspectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InspectionRepository::class)]
class Inspection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_avis = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date_inspection = null;

    #[ORM\Column(length: 255)]
    private ?string $type_inspection = null;

    #[ORM\Column]
    private ?int $inspecteur_id = null;

    #[ORM\Column(length: 255)]
    private ?string $resultat = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'reponse')]
    private ?Avis $avis = null;

    public function __construct()
    {
        $this->date_inspection = new \DateTimeImmutable(); // Définit la date et l'heure actuelles automatiquement
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAvis(): ?int
    {
        return $this->id_avis;
    }

    public function setIdAvis(int $id_avis): static
    {
        $this->id_avis = $id_avis;
        return $this;
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

    public function getInspecteurId(): ?int
    {
        return $this->inspecteur_id;
    }

    public function setInspecteurId(int $inspecteur_id): static
    {
        $this->inspecteur_id = $inspecteur_id;
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
