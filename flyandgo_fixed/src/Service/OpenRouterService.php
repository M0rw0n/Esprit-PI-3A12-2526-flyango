<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenRouterService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        ?string $openRouterApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->apiKey = $openRouterApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && str_starts_with($this->apiKey, 'sk-or-');
    }

    public function chat(string $prompt): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenRouter API non configurée'
            ];
        }

        try {
            $response = $this->httpClient->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'https://flyandgo.tn',
                    'Title' => 'Fly&Go Messenger'
                ],
                'json' => [
                    'model' => 'openai/gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 1000,
                ],
                'timeout' => 60,
            ]);

            $data = $response->toArray();

            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'response' => trim($data['choices'][0]['message']['content'])
                ];
            }

            return [
                'success' => false,
                'error' => 'Format de réponse inattendu'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}