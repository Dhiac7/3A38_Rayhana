<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PredictionService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function predict(array $productData): int
{
    try {
        $response = $this->httpClient->request(
            'POST', 
            'http://localhost:5000/predict',
            [
                'json' => [
                    'article' => $productData['nom'], // Nom exact du champ
                    'unit_price' => (float)$productData['prix'],
                    'day_of_week' => ($productData['jour_semaine'] + 6) % 7, // Ajustement Python
                    'month' => (int)$productData['mois'],
                    'total_price' => (float)$productData['prix'] * 1 // Quantité par défaut
                ],
                'timeout' => 3 // Timeout de 3 secondes
            ]
        );

        $data = $response->toArray();
        return (int)round($data['prediction'] ?? 1); // Conversion en entier

    } catch (\Exception $e) {
        // Journalisation des erreurs
        $this->logger->error('Erreur de prédiction : '.$e->getMessage());
        return 1; // Valeur de secours
    }
}
}