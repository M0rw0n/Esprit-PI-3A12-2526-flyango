<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ModerationService
{
    private ?HttpClientInterface $client = null;
    private string $perspectiveApiKey;

    public function __construct(string $perspectiveApiKey = '')
    {
        $this->perspectiveApiKey = $perspectiveApiKey ?: $_ENV['GOOGLE_PERSPECTIVE_API_KEY'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function analyzeText(string $text): array
    {
        if (empty($this->perspectiveApiKey)) {
            return $this->getMockModeration($text);
        }

        try {
            $response = $this->getClient()->request('POST', 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze', [
                'query' => ['key' => $this->perspectiveApiKey],
                'json' => [
                    'comment' => ['text' => $text],
                    'languages' => ['en', 'fr', 'es'],
                    'requestedAttributes' => [
                        'TOXICITY' => [],
                        'SEVERE_TOXICITY' => [],
                        'INSULT' => [],
                        'THREAT' => [],
                        'SPAM' => []
                    ]
                ]
            ]);

            $data = $response->toArray();
            return $this->parseModerationResponse($data);
        } catch (\Exception $e) {
            return $this->getMockModeration($text);
        }
    }

    public function checkToxicity(string $text, float $threshold = 0.5): array
    {
        $result = $this->analyzeText($text);
        
        $isToxic = ($result['scores']['TOXICITY'] ?? 0) > $threshold;
        
        return [
            'success' => true,
            'is_toxic' => $isToxic,
            'scores' => $result['scores'],
            'recommendation' => $isToxic ? 'reject' : 'approve'
        ];
    }

    public function batchAnalyze(array $texts): array
    {
        $results = [];
        foreach ($texts as $index => $text) {
            $results[$index] = $this->checkToxicity($text);
        }
        return ['success' => true, 'results' => $results];
    }

    private function parseModerationResponse(array $data): array
    {
        $scores = [];
        if (isset($data['attributeScores'])) {
            foreach ($data['attributeScores'] as $attribute => $scoreData) {
                $scores[strtoupper($attribute)] = $scoreData['summaryScore']['value'] ?? 0;
            }
        }

        return [
            'success' => true,
            'scores' => $scores,
            'is_toxic' => ($scores['TOXICITY'] ?? 0) > 0.5
        ];
    }

    private function getMockModeration(string $text): array
    {
        $toxicKeywords = [
            'merde', 'putain', 'chier', 'bordel', 'salope', 'connard', 'enfant de pute', 'fuck', 'shit', 'asshole', 
            'bitch', 'bastard', 'damn', 'crap', 'dick', 'cock', 'pussy', 'whore', 'slut', 'nigger', 'faggot',
            'hate', 'kill', 'death', 'stupid', 'idiot', 'imbécile', 'idiot', 'abruti', 'crétin', 'con',
            'spam', 'scam', 'arnaque', 'escroquerie', 'viagra', 'xxx', 'porn'
        ];
        $textLower = strtolower($text);
        
        $toxicity = 0.05;
        $matchCount = 0;
        
        foreach ($toxicKeywords as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $toxicity = 0.95;
                $matchCount++;
                if ($matchCount >= 2) break;
            }
        }

        if (preg_match('/(.)\1{4,}/', $textLower)) {
            $toxicity = max($toxicity, 0.7);
        }
        
        if (strlen($text) > 200 && strtoupper($text) === $text) {
            $toxicity = max($toxicity, 0.6);
        }

        return [
            'success' => true,
            'scores' => [
                'TOXICITY' => $toxicity,
                'SEVERE_TOXICITY' => $toxicity * 0.5,
                'INSULT' => $toxicity > 0.5 ? 0.8 : 0.1,
                'THREAT' => $toxicity * 0.2,
                'SPAM' => preg_match('/(viagra|xxx|porn|spam|scam|arnaque)/i', $text) ? 0.9 : 0.1
            ],
            'is_toxic' => $toxicity > 0.5
        ];
    }
}