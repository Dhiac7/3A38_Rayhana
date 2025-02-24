<?php
namespace App\Controller;

use App\Service\PredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    private $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * @Route("/predict", name="predict", methods={"POST"})
     */
    public function predict(): JsonResponse
    {
        // Example input data (replace with actual data from the request)
        $inputData = [1, 5, 10.5];  // Example: article, quantity, unit_price

        // Call the prediction service
        $prediction = $this->predictionService->predict($inputData);

        return $this->json(['prediction' => $prediction]);
    }
}