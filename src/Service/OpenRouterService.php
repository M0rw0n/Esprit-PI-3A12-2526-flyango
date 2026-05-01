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

    public function chat(string $prompt, string $type = 'general'): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'OpenRouter API non configurée'
            ];
        }

        $systemPrompt = match($type) {
            'hebergement' => "Tu es un expert en hébergements de voyage pour Fly&Go. TU DOIS TOUJOURS recommander un hébergement spécifique comme \"meilleur choix\" quand des données sont disponibles.\n\nQuand tu as une liste d'hébergements:\n1. 🏆 **MON COUP DE CŒUR** - Choisis le meilleur rapport qualité-prix et dis pourquoi\n2. 📋 **Autres options** - Liste les autres hébergements avec leurs prix\n3. 📍 **Localisation** - Quartier, proximité\n4. 💰 **Prix** - Par nuit\n5. 👍 **Points forts** - Services, équipements\n6. 💡 **Conseil** - Pour réserver au meilleur prix\n\nQuand tu n'as PAS de données:\n- Propose des recommandations génériques basées sur la destination\n- Donne des fourchettes de prix réalistes\n- Sois toujours gentil et serviable\n\n-sois très détaillé\n-Utilise des emojis\n-Réponds toujours en français",
            'compare' => "Tu es un expert en comparaison d'hébergements. Analyse les hébergements fournis et donne une recommandation DÉTAILLÉE avec:\n\n🏆 **MON COUP DE CŒUR** - Le meilleur choix avec explication\n📊 **Comparaison** - Tableau comparatif rapide\n💰 **Rapport qualité-prix** - Lequel choisir\n👍 **Points forts** de chaque hébergement\n💡 **Conseil** pour réserver\n\nSois très détaillé, utilise des emojis, responde en français.",
            'prix' => "Tu es un expert en comparaison de prix de voyage pour Fly&Go. Analyse la demande et fournis une réponse TRÈS DÉTAILLÉE en français avec:\n\n1. 💵 **Estimation de prix** - Fourchette réaliste pour le type de voyage\n2. 📊 **Facteurs** - Ce qui influence le plus le prix (saison,提前, compagnie)\n3. 💡 **Astuces** - 3-4 conseils concrets pour économiser\n4. 📅 **Meilleure période** - Quand réserver pour avoir le meilleur prix\n5. 🔄 **Alternatives** - Options moins chères ou meilleur rapport qualité-prix\n\n-sois très détaillé et concret\n-Utilise des exemples chiffrés\n-Utilise des emojis",
            default => "Tu es un assistant de voyage professionnel pour Fly&Go. Aide les utilisateurs à planifier leur voyage: vols, hébergements, circuits, activités. Sois TRÈS DÉTAILLÉ dans tes réponses, utilise des emojis, donne des exemples concrets avec des prix estimatifs quand c'est pertinent. Réponds toujours en français de manière chaleureuse et conversationnelle."
        };

        try {
            $response = $this->httpClient->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'https://flyandgo.tn',
                    'Title' => 'Fly&Go Messenger'
                ],
                'json' => [
                    'model' => 'meta-llama/llama-3.1-8b-instruct',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 1500,
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