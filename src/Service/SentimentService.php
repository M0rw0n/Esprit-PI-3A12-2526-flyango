<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SentimentService
{
    private ?string $apiKey;
    private ?string $hfApiKey;
    private HttpClientInterface $httpClient;
    private ?CacheInterface $cache;
    private array $positiveWords = [];
    private array $negativeWords = [];

    public function __construct(
        ?string $googleApiKey = null,
        ?string $huggingFaceApiKey = null,
        ?HttpClientInterface $httpClient = null,
        ?CacheInterface $cache = null
    ) {
        $this->apiKey = $googleApiKey;
        $this->hfApiKey = $huggingFaceApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->cache = $cache;
        $this->initLexicon();
    }

    private function initLexicon(): void
    {
        $this->positiveWords = [
            'excellent', 'parfait', 'superbe', 'magnifique', 'splendide', 'génial', 'fantastique',
            'merveilleux', 'incroyable', ' fabuleux', 'sublime', 'formidable', 'impeccable', 'au top',
            'recommande', 'satisfait', 'content', 'ravi', 'enchante', 'qualité', 'propre', 'confortable',
            'personnel', 'accueil', 'sympathique', 'souriant', 'professionnel', 'attentionné', 'parfait',
            'bien', 'bon', 'super', 'nickel', 'impeccable', 'adore', 'love', 'perfect', 'great',
            'amazing', 'wonderful', 'best', 'outstanding', 'exceptional', 'bravo', 'felicitation',
            'emplacement', 'vue', 'délicieux', 'buffet', 'spa', 'piscine', 'calme', 'relaxant',
            'service impeccable', 'rapide', 'efficace', 'moderne', 'élégant'
        ];

        $this->negativeWords = [
            'horrible', 'terrible', 'nuit', 'décevant', 'déçu', 'catastrophique', 'pire', 'horrible',
            'sale', 'malpropre', 'dégoûtant', 'infect', 'pourri', 'mauvais', 'pas bien', ' null',
            'zero', 'jamais', 'refus', 'problème', 'erreur', 'arnaque', 'vol', 'faux', 'menteur',
            'incompétent', 'impoli', 'grossier', 'arrogant', 'méfiant', 'horaire', 'retard', 'attente',
            'bruyant', 'inconfortable', 'étroit', 'vieux', 'dépassé', 'cassé', 'ne fonctionne pas',
            'froid', 'chaud', 'mal空调', 'insecte', 'punaise', 'odeur', 'fleure', 'malodorant',
            'prix', 'cher', 'exorbitant', 'volonté', 'arnaque', 'dégarni', 'vide', 'rien',
            'aucun', 'jamais', 'personne', 'aucune', 'zero', 'médiocre', 'banal', 'ordinnaire',
            'pas recommende', 'ne。建议', 'à éviter', 'poubelle', 'fuite', 'inondation'
        ];
    }

    public function isApiEnabled(): bool
    {
        return !empty($this->hfApiKey) && $this->hfApiKey !== 'your_huggingface_token_here';
    }

    public function isGoogleApiEnabled(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_google_api_key_here';
    }

    public function analyze(string $text): array
    {
        $text = trim($text);
        
        if (empty($text)) {
            return $this->getDefaultResult();
        }

        if ($this->isApiEnabled() && $this->cache) {
            $cacheKey = 'sentiment_' . md5($text);
            try {
                return $this->cache->get($cacheKey, function(ItemInterface $item) use ($text) {
                    $item->expiresAfter(3600);
                    return $this->analyzeWithHuggingFace($text);
                });
            } catch (\Exception $e) {
                return $this->analyzeLocally($text);
            }
        }

        if ($this->isApiEnabled()) {
            return $this->analyzeWithHuggingFace($text);
        }

        if ($this->isGoogleApiEnabled()) {
            return $this->analyzeWithGoogle($text);
        }

        return $this->analyzeLocally($text);
    }

    private function analyzeWithHuggingFace(string $text): array
    {
        try {
            $response = $this->httpClient->request('POST', 
                'https://api-inference.huggingface.co/models/distilbert-base-uncased-finetuned-sst-2-english',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->hfApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => $text,
                    ],
                    'timeout' => 30,
                ]
            );

            $data = $response->toArray();
            
            if (isset($data[0]) && is_array($data[0])) {
                $bestResult = null;
                $bestScore = 0;
                
                foreach ($data[0] as $result) {
                    if ($result['score'] > $bestScore) {
                        $bestScore = $result['score'];
                        $bestResult = $result;
                    }
                }
                
                if ($bestResult) {
                    $label = $bestResult['label'] ?? 'NEUTRAL';
                    $score = $bestResult['score'] ?? 0.5;
                    
                    $normalizedScore = match($label) {
                        'POSITIVE' => $score,
                        'NEGATIVE' => -$score,
                        default => 0
                    };

                    return $this->convertToBusinessData($normalizedScore, $score, $text);
                }
            }

            return $this->analyzeLocally($text);

        } catch (\Exception $e) {
            return $this->analyzeLocally($text);
        }
    }

    private function analyzeWithGoogle(string $text): array
    {
        try {
            $response = $this->httpClient->request('POST', 
                'https://language.googleapis.com/v1/documents:analyzeSentiment?key=' . $this->apiKey,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'document' => [
                            'type' => 'PLAIN_TEXT',
                            'language' => 'fr',
                            'content' => $text
                        ],
                        'encodingType' => 'UTF8'
                    ]
                ]
            );

            $data = $response->toArray();
            
            if (isset($data['documentSentiment'])) {
                $score = $data['documentSentiment']['score'] ?? 0;
                $magnitude = $data['documentSentiment']['magnitude'] ?? 0;
                
                return $this->convertToBusinessData($score, $magnitude, $text);
            }

            return $this->analyzeLocally($text);

        } catch (\Exception $e) {
            return $this->analyzeLocally($text);
        }
    }

    private function analyzeLocally(string $text): array
    {
        $textLower = mb_strtolower($text, 'UTF-8');
        $words = preg_split('/\s+/', $textLower, -1, PREG_SPLIT_NO_EMPTY);
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^a-zà-ÿ]/u', '', $word);
            
            foreach ($this->positiveWords as $posWord) {
                if (mb_stripos($cleanWord, $posWord) !== false || mb_stripos($posWord, $cleanWord) !== false) {
                    $positiveCount++;
                    break;
                }
            }
            
            foreach ($this->negativeWords as $negWord) {
                if (mb_stripos($cleanWord, $negWord) !== false || mb_stripos($negWord, $cleanWord) !== false) {
                    $negativeCount++;
                    break;
                }
            }
        }

        $total = $positiveCount + $negativeCount;
        
        if ($total === 0) {
            return [
                'score' => 0.0,
                'magnitude' => 0.0,
                'label' => 'neutral',
                'stars' => 3,
                'category' => 'Average',
                'confidence' => 0.3,
                'source' => 'local'
            ];
        }

        $score = ($positiveCount - $negativeCount) / max($total, 3);
        $magnitude = ($positiveCount + $negativeCount) / mb_strlen($text) * 10;
        
        return $this->convertToBusinessData($score, $magnitude, $text);
    }

    private function convertToBusinessData(float $score, float $magnitude, string $text): array
    {
        $normalizedScore = max(-1, min(1, $score));
        
        $label = match(true) {
            $normalizedScore > 0.5 => 'excellent',
            $normalizedScore > 0.25 => 'good',
            $normalizedScore > 0 => 'positive',
            $normalizedScore > -0.25 => 'neutral',
            $normalizedScore > -0.5 => 'negative',
            default => 'bad'
        };

        $stars = match(true) {
            $normalizedScore > 0.7 => 5,
            $normalizedScore > 0.4 => 4,
            $normalizedScore > 0.1 => 3,
            $normalizedScore > -0.1 => 2,
            $normalizedScore > -0.4 => 1,
            default => 0
        };

        $category = match($label) {
            'excellent', 'good', 'positive' => 'Top Rated',
            'neutral' => 'Average',
            default => 'Not Recommended'
        };

        $confidence = min(1.0, abs($normalizedScore) + ($magnitude / 10));

        $source = 'local';
        if ($this->isApiEnabled()) {
            $source = 'huggingface';
        } elseif ($this->isGoogleApiEnabled()) {
            $source = 'google_api';
        }

        return [
            'score' => round($normalizedScore, 3),
            'magnitude' => round($magnitude, 3),
            'label' => $label,
            'stars' => $stars,
            'category' => $category,
            'confidence' => round($confidence, 2),
            'source' => $source
        ];
    }

    private function getDefaultResult(): array
    {
        return [
            'score' => 0.0,
            'magnitude' => 0.0,
            'label' => 'neutral',
            'stars' => 3,
            'category' => 'Average',
            'confidence' => 0.0,
            'source' => 'none'
        ];
    }

    public function analyzeMultiple(array $reviews): array
    {
        $results = [];
        $totalScore = 0;
        $positive = 0;
        $negative = 0;
        $neutral = 0;

        foreach ($reviews as $review) {
            $text = is_array($review) ? ($review['commentaire'] ?? $review['comment'] ?? '') : (method_exists($review, 'getCommentaire') ? $review->getCommentaire() : '');
            
            $analysis = $this->analyze($text);
            $results[] = array_merge(['review' => $review], $analysis);

            $totalScore += $analysis['score'];
            
            if ($analysis['stars'] >= 4) $positive++;
            elseif ($analysis['stars'] <= 2) $negative++;
            else $neutral++;
        }

        $count = count($reviews);
        
        return [
            'analyzed_reviews' => $results,
            'summary' => [
                'total_reviews' => $count,
                'average_score' => $count > 0 ? round($totalScore / $count, 3) : 0,
                'average_stars' => $count > 0 ? round(array_sum(array_column($results, 'stars')) / $count, 1) : 0,
                'positive_count' => $positive,
                'negative_count' => $negative,
                'neutral_count' => $neutral,
                'satisfaction_rate' => $count > 0 ? round(($positive / $count) * 100, 1) : 0,
                'recommendation' => $count > 0 ? ($positive > $negative ? 'Recommandé' : 'Non recommandé') : 'N/A'
            ]
        ];
    }

    public function extractKeywords(string $text, int $limit = 5): array
    {
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'car', 'que', 'qui', 'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'mon', 'ma', 'mes', 'ton', 'ta', 'tes', 'son', 'sa', 'ses', 'ce', 'cet', 'cette', 'ces', 'dans', 'sur', 'sous', 'avec', 'sans', 'pour', 'par', 'en', 'est', 'sont', 'été', 'fait', 'était', 'étaient', 'être', 'avoir', 'faire', 'pouvoir', 'vouloir', 'devoir', 'oui', 'non', 'bien', 'mal', 'plus', 'moins', 'aussi', 'très', 'plus', 'peut', 'être', 'cette', 'nous', 'comme', 'avec', 'pour', 'mais', 'sont', 'était', 'été', 'fait', 'tres', 'chez', 'pas', 'même', 'donc', 'on'];
        
        $textLower = mb_strtolower($text, 'UTF-8');
        $words = preg_split('/[\s,.:;!?\'"()\[\]]+/', $textLower, -1, PREG_SPLIT_NO_EMPTY);
        
        $wordFreq = [];
        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^a-zà-ÿ]/u', '', $word);
            if (mb_strlen($cleanWord) > 3 && !in_array($cleanWord, $stopWords)) {
                $wordFreq[$cleanWord] = ($wordFreq[$cleanWord] ?? 0) + 1;
            }
        }
        
        arsort($wordFreq);
        
        $topWords = array_slice($wordFreq, 0, $limit, true);
        
        $keywords = [];
        foreach ($topWords as $word => $count) {
            $sentiment = 'neutral';
            foreach ($this->positiveWords as $pw) {
                if (mb_stripos($word, $pw) !== false) {
                    $sentiment = 'positive';
                    break;
                }
            }
            if ($sentiment === 'neutral') {
                foreach ($this->negativeWords as $nw) {
                    if (mb_stripos($word, $nw) !== false) {
                        $sentiment = 'negative';
                        break;
                    }
                }
            }
            $keywords[] = ['word' => $word, 'count' => $count, 'sentiment' => $sentiment];
        }
        
        return $keywords;
    }
}