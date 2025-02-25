<?php
namespace App\Controller;

use App\Service\PredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    private $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    #[Route('/predict', name: 'predict', methods: ['POST'])]
    public function predict(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation des données
            $requiredKeys = ['article', 'unit_price', 'day_of_week', 'month'];
            foreach ($requiredKeys as $key) {
                if (!isset($data[$key])) {
                    return $this->json([
                        'error' => 'Données manquantes',
                        'missing' => $key
                    ], 400);
                }
            }

            $prediction = $this->predictionService->predict([
                $data['article'],
                $data['unit_price'],
                $data['day_of_week'],
                $data['month']
            ]);

            return $this->json([
                'prediction' => $prediction,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}