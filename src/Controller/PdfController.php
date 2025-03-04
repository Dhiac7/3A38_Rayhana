<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CultureAgricole;
use Doctrine\ORM\EntityManagerInterface;

class PdfController extends AbstractController
{
    private $snappy;
    private $entityManager;

    public function __construct(Pdf $snappy, EntityManagerInterface $entityManager)
    {
        $this->snappy = $snappy;
        $this->entityManager = $entityManager;
    }

    #[Route('/pdf/{id}', name: 'pdf')]
    public function generatePdf(Request $request, $id): Response
    {
        // Récupérer l'entité CultureAgricole depuis la base de données
        $cultureAgricole = $this->entityManager->getRepository(CultureAgricole::class)->find($id);

        if (!$cultureAgricole) {
            throw $this->createNotFoundException('Culture agricole non trouvée pour l\'id ' . $id);
        }

        // Préparer les données pour le template Twig
        $data = [
            'id' => $cultureAgricole->getId(),
            'nom' => $cultureAgricole->getNom(),
            'rendementEstime' => $cultureAgricole->getRendementEstime(),
            'statut' => $cultureAgricole->getStatut(),
            'dateSemi' => $cultureAgricole->getDateSemi(),
            'type' => $cultureAgricole->getType(),
            'superficie' => $cultureAgricole->getSuperficie(),
            'climat' => 'Tempéré chaud', // Exemple de donnée supplémentaire
            'typeSol' => 'Sol limoneux', // Exemple de donnée supplémentaire
            'irrigation' => 'Tous les 3 jours', // Exemple de donnée supplémentaire
            'engrais' => 'Engrais azoté NPK', // Exemple de donnée supplémentaire
            'parcelles' => [], // Vous pouvez remplir ce tableau avec les parcelles associées
        ];

        // Récupérer les parcelles associées
        foreach ($cultureAgricole->getParcelles() as $parcelle) {
            $data['parcelles'][] = [
                'id' => $parcelle->getId(),
                'nom' => $parcelle->getNom(),
                'superficie' => $parcelle->getSuperficie(),
            ];
        }

        $dateRecolteEstimee = $cultureAgricole->getDateRecolteEstimee();
        $traitementsPlanifies = $cultureAgricole->getTraitementsPlanifies();
        $nextIntervention = null;

        // Calcul de la prochaine intervention
        $now = new \DateTime();
        foreach ($traitementsPlanifies as $traitement) {
            if ($traitement['date'] > $now) {
                $nextIntervention = $traitement;
                break;
            }
        }

        // Générer la vue HTML avec les données
        $html = $this->renderView('pdf/index.html.twig', [
            'culture_agricole' => $data,
            'date_recolte_estimee' => $dateRecolteEstimee,
            'traitements_planifies' => $traitementsPlanifies,
            'next_intervention' => $nextIntervention,
            'now' => new \DateTime() // Pour les calculs de dates dans Twig
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
            'Content-Disposition' => 'attachment; filename="rapport_culture_' . $id . '.pdf"',
        ]);
    }
}