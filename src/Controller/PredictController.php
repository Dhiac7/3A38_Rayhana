<?php
namespace App\Controller;

use App\Form\PredictType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;

class PredictController extends AbstractController
{
    #[Route('/predict', name: 'app_predict', methods: ['GET', 'POST'])]
    public function predict(Request $request)
    {
        $form = $this->createForm(PredictType::class);
        $form->handleRequest($request);

        $prediction = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('input_data')->getData();

            if ($uploadedFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
                if (!file_exists($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                $newFilename = uniqid('', true) . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move($uploadDirectory, $newFilename);
                } catch (FileException $e) {
                    return $this->render('predict/index.html.twig', [
                        'form'  => $form->createView(),
                        'error' => 'There was an error uploading the file: ' . $e->getMessage(),
                    ]);
                }

                $client = HttpClient::create();
                try {
                    $response = $client->request('POST', 'http://127.0.0.1:5000/predict', [
                        'body' => [
                            'input_data' => fopen($uploadDirectory . '/' . $newFilename, 'r')
                        ]
                    ]);
                    $data = $response->toArray();
                    $prediction = $data['prediction'] ?? 'No prediction returned';
                } catch (\Exception $e) {
                    return $this->render('predict/index.html.twig', [
                        'form'  => $form->createView(),
                        'error' => 'Error contacting the prediction API: ' . $e->getMessage(),
                    ]);
                }
            }
        }

        return $this->render('predict/index.html.twig', [
            'form'       => $form->createView(),
            'prediction' => $prediction,
        ]);
    }
}