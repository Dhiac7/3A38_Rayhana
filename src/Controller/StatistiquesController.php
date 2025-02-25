<?php
// src/Controller/StatistiquesController.php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Atelier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
class StatistiquesController extends AbstractController
{
    #[Route('/api/statistiques', name: 'api_statistiques', methods: ['GET'])]
    public function statistiques(UserRepository $userRepository): JsonResponse
    {
        // Récupérer tous les utilisateurs
        $users = $userRepository->findAll();

        // Initialisation des tableaux pour les statistiques
        $ageGroups = [
            'moins_18' => 0,
            '18_25' => 0,
            '26_35' => 0,
            '36_45' => 0,
            '46_60' => 0,
            'plus_60' => 0
        ];

        $genderCount = [
            'homme' => 0,
            'femme' => 0
        ];

        // Parcours des utilisateurs pour extraire les statistiques
        foreach ($users as $user) {
            // Calcul de l'âge en fonction de l'année de naissance
            $birthYear = $user->getAnneeNaissance();
            $age = date('Y') - $birthYear;

            // Groupes d'âge
            if ($age < 18) {
                $ageGroups['moins_18']++;
            } elseif ($age >= 18 && $age <= 25) {
                $ageGroups['18_25']++;
            } elseif ($age >= 26 && $age <= 35) {
                $ageGroups['26_35']++;
            } elseif ($age >= 36 && $age <= 45) {
                $ageGroups['36_45']++;
            } elseif ($age >= 46 && $age <= 60) {
                $ageGroups['46_60']++;
            } else {
                $ageGroups['plus_60']++;
            }

            // Compter le genre
            if ($user->getGenre() === 'Homme') {
                $genderCount['homme']++;
            } elseif ($user->getGenre() === 'Femme') {
                $genderCount['femme']++;
            }
        }

        // Retourner les statistiques sous forme de réponse JSON
        return new JsonResponse([
            'age_groups' => $ageGroups,
            'gender_count' => $genderCount
        ]);
    }
}   
?>
