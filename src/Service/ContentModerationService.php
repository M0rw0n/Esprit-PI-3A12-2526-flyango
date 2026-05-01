<?php

namespace App\Service;

use Exception;

class ContentModerationService
{
    private array $blockedWords = [
        'merde', 'putain', 'connard', 'enfant de pute', 'salope', 'pute', 'couillon', 
        'fdp', 'ntm', 'ntms', 'batar', 'batard', ' enfoiré', ' pd', 'pede', 'pedophile',
        'encule', 'bite', 'chatte', 'foutre', 'nique', 'ta mere', 'ta mere la pute',
        'wa3', 'walah', 'khouni', 'khouna', 'mofaker', '9arya', 'fra9', 'tes9a',
        'stupide', 'idiot', 'imbecile', 'mongole', 'handicape'
    ];
    
    private string $apiKey;
    
    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?: $_ENV['OPENAI_API_KEY'] ?? $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: ($_SERVER['OPENAI_API_KEY'] ?? '');
    }
    
    public function analyzeContent(string $text): array
    {
        $originalText = $text;
        $text = strtolower($text);
        $foundWords = [];
        $replacements = [];
        
        foreach ($this->blockedWords as $word) {
            if (strpos($text, $word) !== false) {
                $foundWords[] = $word;
                $replacements[$word] = $this->maskWord($word);
            }
        }
        
        if (!empty($foundWords)) {
            return [
                'is_approved' => false,
                'has_offensive' => true,
                'found_words' => $foundWords,
                'replacements' => $replacements,
                'reason' => 'Mots inappropries detectes'
            ];
        }
        
        if (!empty($this->apiKey)) {
            try {
                $apiResult = $this->checkOpenAI($originalText);
                if ($apiResult['flagged']) {
                    return [
                        'is_approved' => false,
                        'has_offensive' => true,
                        'found_words' => $apiResult['categories'] ?? [],
                        'reason' => 'Contenu inapproprie detecte par OpenAI'
                    ];
                }
            } catch (Exception $e) {
                // Continue with local check
            }
        }
        
        return [
            'is_approved' => true,
            'has_offensive' => false,
            'found_words' => [],
            'reason' => 'Contenu approuve'
        ];
    }
    
    private function checkOpenAI(string $text): array
    {
        $prompt = "You are a content moderator. Analyze the following text for offensive or inappropriate content including profanity, insults, hate speech, or inappropriate language in French, English, or Arabic (Darija). 
        
Text to analyze: \"" . $text . "\"

Respond ONLY with a JSON object in this exact format:
{\"flagged\": true/false, \"categories\": [\"category1\", \"category2\"], \"explanation\": \"brief explanation\"}

Categories to use if inappropriate: profanity, insult, hate_speech, sexual_content, violence, discrimination.

If the text is appropriate and does not contain offensive content, respond with {\"flagged\": false, \"categories\": [], \"explanation\": \"Content is appropriate\"}";

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 200
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            $content = trim($content);
            $content = preg_replace('/^[^{]*/', '', $content);
            $content = preg_replace('/[^}]*$/', '', $content);
            
            $result = json_decode($content, true);
            if ($result) {
                return [
                    'flagged' => $result['flagged'] ?? false,
                    'categories' => $result['categories'] ?? [],
                    'explanation' => $result['explanation'] ?? ''
                ];
            }
        }
        
        throw new Exception('OpenAI API unavailable');
    }
    
    public function maskWords(string $text): string
    {
        $masked = $text;
        foreach ($this->blockedWords as $word) {
            if (stripos($text, $word) !== false) {
                $masked = str_ireplace($word, str_repeat('*', strlen($word)), $masked);
            }
        }
        return $masked;
    }
    
    private function maskWord(string $word): string
    {
        return str_repeat('*', strlen($word));
    }
}