<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private string $model;
    private int $maxTokens;

    public function __construct(
        ?string $openAiApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->apiKey = $openAiApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->model = 'gpt-3.5-turbo';
        $this->maxTokens = 500;
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here';
    }

    public function chat(string $userMessage, array $conversationHistory = []): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured'
            ];
        }

        $systemPrompt = $this->getSystemPrompt();
        
        $messages = array_merge([
            ['role' => 'system', 'content' => $systemPrompt]
        ], $conversationHistory, [
            ['role' => 'user', 'content' => $userMessage]
        ]);

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $this->maxTokens,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ],
            ]);

            $data = $response->toArray();

            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'response' => trim($data['choices'][0]['message']['content']),
                    'usage' => $data['usage'] ?? null
                ];
            }

            return [
                'success' => false,
                'error' => 'Unexpected response format'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Tu es l'assistant virtuel de Fly&Go, une agence de voyage tunisienne. 
Tu dois répondre aux questions des clients de manière aimable et professionnelle.

Contexte de Fly&Go:
- Agence de voyage en Tunisie
- Services: hébergements, circuits, transports, activités
- Contact: contact@flyandgo.tn, +216 12 345 678
- Site: vol, hôtel, circuit, activité, transport, forum
- AI pour créer des circuits personnalisés
- Profil voyageur pour personnaliser les recommandations

Règles:
1. Réponds toujours en français
2. Sois concis mais informatif
3. Si tu ne sais pas quelque chose, dis-le honestly et suggère de contacter le support
4. Propose des liens vers les pages appropriées du site
5. Garde un ton professionnel et chaleureux
PROMPT;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function setMaxTokens(int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;
        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
}