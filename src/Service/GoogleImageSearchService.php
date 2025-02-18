<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleImageSearchService
{
    private $client;
    private $apiKey;
    private $cx;

    public function __construct(HttpClientInterface $client, string $apiKey, string $cx)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->cx = $cx;
    }

    public function searchImage(string $query): ?string
    {
        $response = $this->client->request('GET', 'https://www.googleapis.com/customsearch/v1', [
            'query' => [
                'q' => $query,
                'searchType' => 'image',
                'key' => $this->apiKey,
                'cx' => $this->cx,
            ]
        ]);
        
        // Log the full response to see any error details
        $data = $response->toArray();
        dump($data);


        if (!empty($data['items'])) {
            return $data['items'][0]['link']; // Return the first image URL
        }

        return null; // No image found
    }
}
