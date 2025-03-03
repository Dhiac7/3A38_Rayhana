<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PdfController extends AbstractController
{
    private $snappy;

    public function __construct(Pdf $snappy)
    {
        $this->snappy = $snappy;
    }

    #[Route('/pdf/{id}', name: 'pdf')] 
    public function generatePdf(Request $request, $id): Response
    {
        // Simuler ou récupérer les données de la culture agricole avec des détails supplémentaires
        $culture_agricole = [
            'id' => $id,
            'nom' => 'Maïs',
            'rendementEstime' => 5000,
            'statut' => 'En croissance',
            'dateSemi' => new \DateTime('2023-03-01'),
            'type' => 'Céréale',
            'superficie' => 120,
            'climat' => 'Tempéré chaud',
            'typeSol' => 'Sol limoneux',
            'irrigation' => 'Tous les 3 jours',
            'engrais' => 'Engrais azoté NPK',
            'parcelles' => [
                ['id' => 1, 'nom' => 'Parcelle A', 'superficie' => 50],
                ['id' => 2, 'nom' => 'Parcelle B', 'superficie' => 70]
            ]
        ];

        // Générer la vue HTML avec les données mises à jour
        $html = $this->renderView('pdf/index.html.twig', [
            'culture_agricole' => $culture_agricole,
        ]);

        // Convertir le HTML en PDF
        try {
            $pdf = $this->snappy->getOutputFromHtml($html);
        } catch (\Exception $e) {
            throw new \RuntimeException('Échec de la génération du PDF : ' . $e->getMessage());
        }

        // Retourner la réponse avec le PDF
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_culture_' . $id . '.pdf"'
        ]);
    }
}
