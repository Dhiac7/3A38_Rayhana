<?php

// src/Repository/AtelierRepository.php
namespace App\Repository;

use App\Entity\Atelier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AtelierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atelier::class);
    }

    // Exemple de méthode personnalisée
    public function findUpcomingAteliers(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.startAt > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}