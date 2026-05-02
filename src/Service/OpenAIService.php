<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class OpenAIService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private ?CacheInterface $cache;
    private string $model;
    private int $maxTokens;

    public function __construct(
        ?string $openAiApiKey = null,
        ?HttpClientInterface $httpClient = null,
        ?CacheInterface $cache = null
    ) {
        $this->apiKey = $openAiApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->cache = $cache;
        $this->model = 'gpt-4o-mini'; // Upgraded from gpt-3.5-turbo for better performance/cost
        $this->maxTokens = 500;
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here';
    }

    public function chat(string $userMessage, array $conversationHistory = [], ?array $userContext = null): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured'
            ];
        }

        // Use cache for identical queries only if history is empty
        if ($this->cache && empty($conversationHistory)) {
            $cacheKey = 'openai_v2_' . md5(mb_strtolower(trim($userMessage)) . ($userContext ? serialize($userContext) : ''));
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userMessage, $conversationHistory, $userContext) {
                $item->expiresAfter(86400); // 24 hours for static answers
                return $this->callApi($userMessage, $conversationHistory, $userContext);
            });
        }

        return $this->callApi($userMessage, $conversationHistory, $userContext);
    }

    public function generateEmbedding(string $text): ?array
    {
        if (!$this->isEnabled()) return null;

        $cacheKey = 'embedding_' . md5(mb_strtolower(trim($text)));
        if ($this->cache) {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($text) {
                $item->expiresAfter(3600 * 24 * 30); // 30 days
                return $this->callEmbeddingApi($text);
            });
        }
        return $this->callEmbeddingApi($text);
    }

    private function callEmbeddingApi(string $text): ?array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/embeddings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'text-embedding-3-small',
                    'input' => $text,
                    'dimensions' => 1536
                ],
                'verify_peer' => false,
                'verify_host' => false,
            ]);

            $data = $response->toArray();
            if (empty($data['data'][0]['embedding'])) {
                error_log("OpenAI Embedding Error: Empty embedding returned for text: " . $text);
            }
            return $data['data'][0]['embedding'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function callApi(string $userMessage, array $conversationHistory = [], ?array $userContext = null): array
    {
        $systemPrompt = $this->getSystemPrompt($userContext);
        
        // Limit history to last 5 messages to save tokens and speed up response
        $history = array_slice($conversationHistory, -5);

        $messages = array_merge([
            ['role' => 'system', 'content' => $systemPrompt]
        ], $history, [
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
                ],
                'verify_peer' => false,
                'verify_host' => false,
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

    private function getSystemPrompt(?array $userContext = null): string
    {
        $userName = $userContext['name'] ?? 'Voyageur';
        $userRole = $userContext['role'] ?? 'utilisateur';
        
        return <<<PROMPT
Tu es l'assistant virtuel expert de Fly&Go, la plateforme leader du voyage en Tunisie. 
Tu parles à $userName (un $userRole de la plateforme).

Ton objectif est d'aider les utilisateurs à planifier leurs voyages, réserver des services et répondre à leurs questions avec enthousiasme, précision et une expertise locale approfondie.

Expertise Fly&Go & Contexte Tunisien:
- Hébergements: Hôtels 5* à Hammamet, maisons d'hôtes authentiques à Sidi Bou Said, villas avec piscine à Djerba, campements de luxe à Douz.
- Circuits: La Route des Oasis, les sites de tournage de Star Wars (Tataouine, Matmata), le Grand Sud, les Médinas de Tunis et Sousse.
- Transports: Vols vers Tunis-Carthage, Monastir, Djerba-Zarzis. Location de voitures avec ou sans chauffeur.
- Activités: Thalassothérapie, Kitesurf à Djerba, Quad dans le Sahara, dégustation culinaire (Couscous, Brik, Lablabi).

Directives de réponse:
1. Ton: Professionnel, chaleureux, expert local passionné. Adresse-toi à l'utilisateur par son nom ($userName) si approprié.
2. Monnaie: Utilise le Dinar Tunisien (TND) ou l'Euro (€) selon le contexte.
3. Format: Utilise des émojis pertinents, des listes à puces et du gras pour la lisibilité.
4. Rapidité: Sois efficace. Ne dépasse pas 3-4 paragraphes.
5. Call-to-action: Dirige toujours vers les sections [Hébergements](/hebergements), [Circuits](/circuits), [Transports](/transports) ou [Activités](/activites) du site Fly&Go.
6. Langue: Français impeccable, avec quelques expressions de bienvenue tunisiennes (ex: "Aslama", "Marhba") si approprié.

Conseil Expert: Si un utilisateur demande un itinéraire, inclus des joyaux cachés moins connus pour montrer ton expertise.
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