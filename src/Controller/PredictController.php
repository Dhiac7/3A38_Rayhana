<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class PredictController extends AbstractController
{

    #[Route('/predict/model1', name: 'predict_model1', methods: ['GET', 'POST'])]
    public function predictModel1(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {


        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $prediction = null;
        $error = null;
        $newFilePath = null;

        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');
            $tempPath = $file->getPathname(); // Chemin temporaire
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilePath = sys_get_temp_dir() . '/' . $originalFilename . '.' . $file->guessExtension();

            copy($tempPath, $newFilePath); // Copier le fichier dans un dossier temporaire stable

            if (!$file) {
                $error = "Aucun fichier envoyé.";
            } else {
                $client = HttpClient::create();
                try {
                    $response = $client->request('POST', 'http://127.0.0.1:5000/model1/predict_model1', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'multipart/form-data', 
                            
                        ],
                        'body' => [
                            'file' => fopen($newFilePath, 'r'),
                        ],
                    ]);

                    $data = $response->toArray();
                    $prediction = $data['prediction'] ?? "Réponse invalide du serveur";
                } catch (\Exception $e) {
                    $error = "Erreur lors de la prédiction : " . $e->getMessage();
                }
            }
        }

        return $this->render('predict/model1.html.twig', [
            'prediction' => $prediction,
            'error' => $newFilePath,
            'loggedInUser' => $loggedInUser,

        ]);
    }

    #[Route('/predict/model2', name: 'predict_model2', methods: ['GET', 'POST'])]
    public function predictModel2(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $prediction = null;
        $calories = null;
        $error = null;
        $newFilePath = null;

        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');
            $tempPath = $file->getPathname(); // Chemin temporaire
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilePath = sys_get_temp_dir() . '/' . $originalFilename . '.' . $file->guessExtension();

            if (!$file) {
                $error = "Aucun fichier envoyé.";
            } else {
                // Envoyer l'image à l'API Flask pour model2
                $client = HttpClient::create();
                try {
                    $response = $client->request('POST', 'http://127.0.0.1:5000/model2/predict_model2', [
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'body' => [
                            'file' => fopen($newFilePath, 'r'),
                        ],
                    ]);

                    $data = $response->toArray();
                    $prediction = $data['prediction'];
                    $calories = $data['calories_per_100g'];
                } catch (\Exception $e) {
                    $error = "Erreur lors de la prédiction : " . $e->getMessage();
                }
            }
        }

        return $this->render('predict/model2.html.twig', [
            'prediction' => $prediction,
            'calories' => $calories,
            'error' => $error,
            'loggedInUser' => $loggedInUser,

        ]);
    }
}