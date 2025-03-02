<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Atelier; // Assurez-vous d'importer l'entité Atelier
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // ... (vos autres méthodes existantes)

    /**
     * Récupère les statistiques des participants par genre pour un atelier donné.
     */
    public function getUserStatisticsByAtelier(Atelier $atelier): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.genre AS genre, COUNT(u.id) AS user_count')
            ->join('u.participations', 'p') // Supposons que 'participations' est la relation entre User et Atelier
            ->where('p.atelier = :atelier')
            ->setParameter('atelier', $atelier)
            ->groupBy('u.genre')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les statistiques des participants par tranche d'âge pour un atelier donné.
     */
    public function getAgeStatisticsByAtelier(Atelier $atelier): array
    {
        $users = $this->createQueryBuilder('u')
            ->join('u.participations', 'p') // Supposons que 'participations' est la relation entre User et Atelier
            ->where('p.atelier = :atelier')
            ->setParameter('atelier', $atelier)
            ->getQuery()
            ->getResult();

        $ageGroups = [
            ['age_range' => '0-18', 'user_count' => 0],
            ['age_range' => '19-35', 'user_count' => 0],
            ['age_range' => '36-50', 'user_count' => 0],
            ['age_range' => '50+', 'user_count' => 0]
        ];

        foreach ($users as $user) {
            $age = $user->getAge(); // Assurez-vous que la méthode getAge() existe dans l'entité User

            if ($age <= 18) {
                $ageGroups[0]['user_count']++;
            } elseif ($age <= 35) {
                $ageGroups[1]['user_count']++;
            } elseif ($age <= 50) {
                $ageGroups[2]['user_count']++;
            } else {
                $ageGroups[3]['user_count']++;
            }
        }

        return $ageGroups;
    }
}