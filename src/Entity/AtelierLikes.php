<?php

namespace App\Entity;

use App\Repository\AtelierLikesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierLikesRepository::class)]
class AtelierLikes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "boolean")]
    private ?bool $is_like = null; // true = like, false = dislike

    #[ORM\ManyToOne(inversedBy: 'atelierLikes')]
    private ?Atelier $atelier = null;

    #[ORM\ManyToOne(inversedBy: 'atelierLikes')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLiked(): ?bool // Renommé pour correspondre à la logique
    {
        return $this->is_like;
    }

    public function setIsLiked(bool $is_like): static // Renommé aussi pour cohérence
    {
        $this->is_like = $is_like;

        return $this;
    }

    public function getAtelier(): ?Atelier
    {
        return $this->atelier;
    }

    public function setAtelier(?Atelier $atelier): static
    {
        $this->atelier = $atelier;

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
}
