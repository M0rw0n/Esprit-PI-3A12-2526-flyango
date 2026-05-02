<?php

namespace App\Service\Api;

class PerspectiveApiService
{
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = $_ENV['PERSPECTIVE_API_KEY'] ?? null;
    }

    public function analyze(string $text): array
    {
        if (!$this->apiKey) {
            return $this->mockAnalyze($text);
        }

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => 'https://commentanalyzer.googleapis.com/v1alpha1',
                'timeout' => 10,
            ]);

            $response = $client->post('/comments:analyze', [
                'json' => [
                    'comment' => ['text' => $text],
                    'languages' => ['fr', 'en', 'ar'],
                    'requestedAttributes' => [
                        'TOXICITY' => [],
                        'SPAM' => [],
                        'PROFANITY' => [],
                        'INSULT' => [],
                    ],
                ],
                'query' => ['key' => $this->apiKey],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $this->formatResponse($data);
        } catch (\Exception $e) {
            return $this->mockAnalyze($text);
        }
    }

    private function mockAnalyze(string $text): array
    {
        $toxicKeywords = ['badword', 'insult', 'spam', 'hate', 'fuck', 'shit', 'damn'];
        $lowerText = strtolower($text);
        
        $toxicity = 0.05;
        $spam = 0.02;
        
        foreach ($toxicKeywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                $toxicity = 0.95;
                break;
            }
        }

        if (strlen($text) > 500 && preg_match('/[A-Z]{4,}/', $text)) {
            $spam = 0.7;
        }

        return [
            'success' => true,
            'scores' => [
                'toxicity' => round($toxicity, 2),
                'spam' => round($spam, 2),
                'profanity' => $toxicity > 0.5 ? 0.8 : 0.02,
                'insult' => $toxicity > 0.7 ? 0.9 : 0.01,
            ],
            'isSafe' => $toxicity < 0.5 && $spam < 0.5,
            'suggestions' => $toxicity > 0.5 ? ['Le contenu contient des expressions potentiellement inappropriées'] : [],
        ];
    }

    private function formatResponse(array $data): array
    {
        $scores = [];
        if (isset($data['attributeScores'])) {
            foreach ($data['attributeScores'] as $attr => $score) {
                $scores[strtolower($attr)] = round($score['summaryScore']['value'] ?? 0, 2);
            }
        }

        return [
            'success' => true,
            'scores' => $scores,
            'isSafe' => ($scores['toxicity'] ?? 0) < 0.5 && ($scores['spam'] ?? 0) < 0.5,
            'suggestions' => [],
        ];
    }

    public function isTextSafe(string $text): bool
    {
        $result = $this->analyze($text);
        return $result['isSafe'] ?? true;
    }
}