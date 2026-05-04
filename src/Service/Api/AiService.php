<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiService
{
    private ?HttpClientInterface $client = null;
    private string $openAiApiKey;

    public function __construct(string $openAiApiKey = '')
    {
        $this->openAiApiKey = $openAiApiKey ?: $_ENV['OPENAI_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

<<<<<<< HEAD
    public function generateResponse(string $prompt, string $model = 'gpt-4o-mini'): array
=======
    public function generateResponse(string $prompt, string $model = 'gpt-3.5-turbo'): array
>>>>>>> testsisi
    {
        if (empty($this->openAiApiKey)) {
            return $this->getMockAiResponse($prompt);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->openAiApiKey}",
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
<<<<<<< HEAD
                        [
                            'role' => 'system', 
                            'content' => 'Tu es un expert en planification de voyages pour Fly&Go. Réponds de manière concise, précise et utilise un ton professionnel et engageant.'
                        ],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 800
=======
                        ['role' => 'system', 'content' => 'You are a helpful travel assistant.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500
>>>>>>> testsisi
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'response' => $data['choices'][0]['message']['content'] ?? '',
                'model' => $model,
                'usage' => $data['usage'] ?? []
            ];
        } catch (\Exception $e) {
            return $this->getMockAiResponse($prompt);
        }
    }

    public function generateCircuitSuggestions(string $destination, array $preferences = []): array
    {
        $preferencesText = !empty($preferences) ? 'Preferences: ' . implode(', ', $preferences) : '';
        $prompt = "Generate a travel itinerary for $destination. $preferencesText Include daily activities, places to visit, and recommended restaurants.";

        return $this->generateResponse($prompt);
    }

    public function analyzeSentiment(string $text): array
    {
        if (empty($this->openAiApiKey)) {
            return $this->getMockSentiment($text);
        }

        $prompt = "Analyze the sentiment of this text and return only: positive, negative, or neutral. Text: $text";
        $response = $this->generateResponse($prompt);

        return [
            'success' => true,
            'sentiment' => strtolower($response['response']),
            'response' => $response['response']
        ];
    }

    public function generateActivityRecommendations(string $location, string $interests): array
    {
        $prompt = "Recommend 5 activities in $location for someone interested in $interests. Format as a JSON array with name, description, and estimated duration for each.";
        return $this->generateResponse($prompt);
    }

    public function summarizeText(string $text, int $maxLength = 100): array
    {
        $prompt = "Summarize this text in $maxLength words or less: $text";
        return $this->generateResponse($prompt);
    }

    private function getMockAiResponse(string $prompt): array
    {
        $location = '';
        if (preg_match('/à\s+([A-Za-zéèêëàâäùûüôöîï]+)/i', $prompt, $matches)) {
            $location = $matches[1];
        } elseif (preg_match('/in\s+([A-Za-z\s]+?)(?:\s+for|$|\.)/i', $prompt, $matches)) {
            $location = trim($matches[1]);
        }

        $responses = [
            'itinerary' => "Voici un exemple d'itinéraire pour votre voyage:\n\nJour 1: Arrivée et visite du centre-ville\nJour 2: Musées et culturelles\nJour 3: Nature etrandonnée\nJour 4: Gastronomie locale\nJour 5: Départ",
            'recommendation' => $location 
                ? "🎯 Recommendations d'activités à $location:\n\n1. 🌊 Excursion plage - Détente et activités nautiques\n2. 🏛️ Visite patrimoine - Musées et monuments historiques\n3. 🍽️ Expérience gastronomique - Cuisine locale traditionnelle\n4. 🏜️ Aventure desert - Safari et dunes\n5. 🛒 Marché local - Shopping artisanal et souvenirs"
                : "1. Visite du vieux quartier\n2. Musée local\n3. Parc naturel\n4. Restaurant typique\n5. Marché artisanal",
            'default' => "Merci pour votre message! Je serais ravi de vous aider avec votre voyage. N'hésitez pas à me poser des questions spécifiques."
        ];

        if (stripos($prompt, 'itinerary') !== false || stripos($prompt, 'itinéraire') !== false) {
            $response = $responses['itinerary'];
        } elseif (stripos($prompt, 'recommend') !== false || stripos($prompt, 'recommander') !== false || stripos($prompt, 'activité') !== false) {
            $response = $responses['recommendation'];
        } else {
            $response = $responses['default'];
        }

        return [
            'success' => true,
            'response' => $response,
            'model' => 'mock-gpt',
            'usage' => ['total_tokens' => 50]
        ];
    }

    private function getMockSentiment(string $text): array
    {
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful', 'love', 'best', 'fantastic'];
        $negativeWords = ['bad', 'terrible', 'awful', 'worst', 'hate', 'poor', 'disappointing'];

        $textLower = strtolower($text);
        
        foreach ($positiveWords as $word) {
            if (strpos($textLower, $word) !== false) {
                return ['success' => true, 'sentiment' => 'positive', 'response' => 'positive'];
            }
        }
        
        foreach ($negativeWords as $word) {
            if (strpos($textLower, $word) !== false) {
                return ['success' => true, 'sentiment' => 'negative', 'response' => 'negative'];
            }
        }

        return ['success' => true, 'sentiment' => 'neutral', 'response' => 'neutral'];
    }
}