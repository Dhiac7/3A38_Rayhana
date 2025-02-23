<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column]
    #[Assert\NotBlank(message: 'La note ne peut pas être vide.')]
    #[Assert\Range(min: 0, max: 10, notInRangeMessage: 'La note doit être entre 0 et 10.')]
    private ?float $rate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le commentaire ne peut pas être vide.')]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?User $client = null;

    /**
     * @var Collection<int, Inspection>
     */
    #[ORM\OneToMany(targetEntity: Inspection::class, mappedBy: 'avis')]
    private Collection $reponse;

    /**
     * @var Collection<int, Inspection>
     */
    #[ORM\OneToMany(targetEntity: Inspection::class, mappedBy: 'avis')]
    private Collection $inspections;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable(); // Définit la date et l'heure actuelles automatiquement
        $this->reponse = new ArrayCollection();
        $this->inspections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }





    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Inspection>
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(Inspection $reponse): static
    {
        if (!$this->reponse->contains($reponse)) {
            $this->reponse->add($reponse);
            $reponse->setAvis($this);
        }

        return $this;
    }

    public function removeReponse(Inspection $reponse): static
    {
        if ($this->reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getAvis() === $this) {
                $reponse->setAvis(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inspection>
     */
    public function getInspections(): Collection
    {
        return $this->inspections;
    }

    public function addInspection(Inspection $inspection): static
    {
        if (!$this->inspections->contains($inspection)) {
            $this->inspections->add($inspection);
            $inspection->setAvis($this);
        }

        return $this;
    }

    public function removeInspection(Inspection $inspection): static
    {
        if ($this->inspections->removeElement($inspection)) {
            // set the owning side to null (unless already changed)
            if ($inspection->getAvis() === $this) {
                $inspection->setAvis(null);
            }
        }

        return $this;
    }
}
