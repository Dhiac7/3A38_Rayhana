<?php
/*namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatAtelierController extends AbstractController
{
    #[Route('/statistics', name: 'admin_statistics')]
    public function statistics(EntityManagerInterface $entityManager): Response
    {
        $conn = $entityManager->getConnection();

        // Requête SQL pour récupérer les statistiques sur le genre
        $genderQuery = "SELECT gender, COUNT(*) as count FROM user 
                        JOIN atelier_user ON user.id = atelier_user.user_id 
                        GROUP BY gender";
        $genderStats = $conn->executeQuery($genderQuery)->fetchAllAssociative();

        // Requête SQL pour récupérer les statistiques par tranche d'âge
        $ageQuery = "SELECT 
                        CASE 
                            WHEN age BETWEEN 0 AND 18 THEN '0-18'
                            WHEN age BETWEEN 19 AND 30 THEN '19-30'
                            WHEN age BETWEEN 31 AND 50 THEN '31-50'
                            ELSE '51+' 
                        END AS age_range, COUNT(*) as count
                    FROM user 
                    JOIN atelier_user ON user.id = atelier_user.user_id
                    GROUP BY age_range";
        $ageStats = $conn->executeQuery($ageQuery)->fetchAllAssociative();

        return $this->render('admin/statistique.html.twig', [
            'genderStats' => $genderStats,
            'ageStats' => $ageStats
        ]);
    }
}
*/
?>
