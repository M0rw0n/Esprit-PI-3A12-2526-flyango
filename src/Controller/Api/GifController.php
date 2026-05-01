<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/gif')]
class GifController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    #[Route('/search', name: 'api_gif_search', methods: ['GET'])]
    public function search(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $query = $request->query->get('q', 'fun');
        
        try {
            $response = $this->httpClient->request('GET', 'https://api.giphy.com/v1/gifs/search', [
                'query' => [
                    'api_key' => 'dc6zaTOxFJmzC',
                    'q' => $query,
                    'limit' => 20,
                    'rating' => 'g'
                ]
            ]);
            
            $data = $response->toArray();
            
            $gifs = array_map(function($gif) {
                return [
                    'id' => $gif['id'],
                    'url' => $gif['images']['original']['url'],
                    'thumb' => $gif['images']['fixed_height_small']['url'],
                    'preview' => $gif['images']['fixed_height_small']['url']
                ];
            }, $data['data'] ?? []);
            
            return new JsonResponse(['success' => true, 'gifs' => $gifs]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/trending', name: 'api_gif_trending', methods: ['GET'])]
    public function trending(): JsonResponse
    {
        try {
            $response = $this->httpClient->request('GET', 'https://api.giphy.com/v1/gifs/trending', [
                'query' => [
                    'api_key' => 'dc6zaTOxFJmzC',
                    'limit' => 20,
                    'rating' => 'g'
                ]
            ]);
            
            $data = $response->toArray();
            
            $gifs = array_map(function($gif) {
                return [
                    'id' => $gif['id'],
                    'url' => $gif['images']['original']['url'],
                    'thumb' => $gif['images']['fixed_height_small']['url'],
                    'preview' => $gif['images']['fixed_height_small']['url']
                ];
            }, $data['data'] ?? []);
            
            return new JsonResponse(['success' => true, 'gifs' => $gifs]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
