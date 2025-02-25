<?php
// src/Controller/RouletteController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RouletteController extends AbstractController
{
    /**
     * @Route("/roulette", name="app_roulette")
     */
    public function index(): Response
    {
        // Les différentes réductions possibles
        $reductions = [
            ['valeur' => '5%', 'probabilite' => 30, 'couleur' => '#FF5252'],
            ['valeur' => '10%', 'probabilite' => 25, 'couleur' => '#448AFF'],
            ['valeur' => '15%', 'probabilite' => 20, 'couleur' => '#66BB6A'],
            ['valeur' => '20%', 'probabilite' => 15, 'couleur' => '#FFC107'],
            ['valeur' => '25%', 'probabilite' => 8, 'couleur' => '#AB47BC'],
            ['valeur' => '50%', 'probabilite' => 2, 'couleur' => '#FF6D00'],
        ];

        return $this->render('roulette/index.html.twig', [
            'reductions' => $reductions,
        ]);
    }

    /**
     * @Route("/roulette/tourner", name="app_roulette_tourner", methods={"POST"})
     */
    public function tourner(Request $request, SessionInterface $session): Response
    {
        // Les différentes réductions possibles
        $reductions = [
            ['valeur' => '5%', 'probabilite' => 30, 'couleur' => '#FF5252'],
            ['valeur' => '10%', 'probabilite' => 25, 'couleur' => '#448AFF'],
            ['valeur' => '15%', 'probabilite' => 20, 'couleur' => '#66BB6A'],
            ['valeur' => '20%', 'probabilite' => 15, 'couleur' => '#FFC107'],
            ['valeur' => '25%', 'probabilite' => 8, 'couleur' => '#AB47BC'],
            ['valeur' => '50%', 'probabilite' => 2, 'couleur' => '#FF6D00'],
        ];

        // Création d'un tableau pondéré selon les probabilités
        $rouletteItems = [];
        foreach ($reductions as $index => $reduction) {
            for ($i = 0; $i < $reduction['probabilite']; $i++) {
                $rouletteItems[] = $index;
            }
        }

        // Sélection aléatoire
        $indexGagnant = $rouletteItems[array_rand($rouletteItems)];
        $reductionGagnee = $reductions[$indexGagnant];

        // Génération d'un code de réduction unique
        $codeReduction = 'PROMO-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Stocker le code de réduction en session
        $codesReductions = $session->get('codes_reductions', []);
        $codesReductions[] = [
            'code' => $codeReduction,
            'pourcentage' => $reductionGagnee['valeur'],
            'dateCreation' => new \DateTime(),
            'estUtilise' => false
        ];
        $session->set('codes_reductions', $codesReductions);

        return $this->json([
            'success' => true,
            'reduction' => $reductionGagnee,
            'code' => $codeReduction,
            'angle' => rand(0, 359), // Angle aléatoire pour l'animation
        ]);
    }
    
    /**
     * @Route("/roulette/codes", name="app_roulette_codes")
     */
    public function afficherCodes(SessionInterface $session): Response
    {
        $codesReductions = $session->get('codes_reductions', []);
        
        return $this->render('roulette/codes.html.twig', [
            'codes' => $codesReductions
        ]);
    }
    
    /**
     * @Route("/roulette/utiliser/{code}", name="app_roulette_utiliser")
     */
    public function utiliserCode(string $code, SessionInterface $session): Response
    {
        $codesReductions = $session->get('codes_reductions', []);
        $success = false;
        $message = "Code non trouvé";
        
        foreach ($codesReductions as $key => $reduction) {
            if ($reduction['code'] === $code && !$reduction['estUtilise']) {
                $codesReductions[$key]['estUtilise'] = true;
                $codesReductions[$key]['dateUtilisation'] = new \DateTime();
                $session->set('codes_reductions', $codesReductions);
                $success = true;
                $message = "Code utilisé avec succès : " . $reduction['pourcentage'] . " de réduction";
                break;
            } elseif ($reduction['code'] === $code && $reduction['estUtilise']) {
                $message = "Ce code a déjà été utilisé";
                break;
            }
        }
        
        $this->addFlash($success ? 'success' : 'error', $message);
        
        return $this->redirectToRoute('app_roulette_codes');
    }
}