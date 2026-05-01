<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MistralService
{
    private ?HttpClientInterface $client = null;
    private string $apiKey;

    public function __construct(string $mistralApiKey = '')
    {
        $this->apiKey = $mistralApiKey ?: $_ENV['MISTRAL_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create([
                'base_uri' => 'https://api.mistral.ai/v1/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);
        }
        return $this->client;
    }

    public function generateProgram(string $destination, array $preferences): string
    {
        if (empty($this->apiKey)) {
            return "Désolé, la génération par Mistral AI n'est pas configurée.";
        }

        $prompt = "Génère un programme de voyage détaillé pour {$destination}. 
        Préférences: " . json_encode($preferences) . ". 
        Le programme doit être en français, structuré par jour avec des activités matin, après-midi et soir.";

        try {
            $response = $this->getClient()->request('POST', 'chat/completions', [
                'json' => [
                    'model' => 'mistral-tiny',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un expert en planification de voyages.'],
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ]
            ]);

            $data = $response->toArray();
            return $data['choices'][0]['message']['content'] ?? 'Erreur lors de la génération du programme.';
        } catch (\Exception $e) {
            return "Erreur Mistral: " . $e->getMessage();
        }
    }
}
