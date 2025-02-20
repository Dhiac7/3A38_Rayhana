<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbrplacedispo = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ateliers')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, User>
     */
    public function getuser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }
}
