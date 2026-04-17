<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private ?string $apiKey;
    private HttpClientInterface $httpClient;
    private array $supportedLanguages = ['FR', 'EN', 'AR', 'DE', 'ES', 'IT', 'PT'];
    private array $cache = [];

    public function __construct(
        ?string $deeplApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->apiKey = $deeplApiKey;
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    public function detectLanguage(string $text): string
    {
        if (preg_match('/[\u0600-\u06FF]/u', $text)) {
            return 'AR';
        }
        if (preg_match('/[a-zA-Z]/', $text)) {
            $textLower = mb_strtolower($text, 'UTF-8');
            
            $words = preg_split('/[\s,.:;!?\'"()\[\]]+/', $textLower, -1, PREG_SPLIT_NO_EMPTY);
            $frenchCount = 0;
            $englishCount = 0;
            
            $frenchWords = ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'est', 'pour', 'avec', 'dans', 'ce', 'cette', 'sur', 'plus', 'pas', 'mais', 'leur', 'nous', 'vous', 'qui', 'que', 'quoi', 'comment', 'pourquoi', 'quand', 'où', 'hotel', 'chambre', 'reservation', 'nuit', 'prix', 'vue', 'merci', 'bonjour', 'hôtel', 'chambre', 'réservation', 'nuit', 'prix', 'vue', 'merci', 'bonjour', 'accueil', 'service', 'personnel'];
            $englishWords = ['the', 'a', 'an', 'is', 'are', 'for', 'with', 'in', 'this', 'that', 'on', 'more', 'not', 'but', 'their', 'we', 'you', 'who', 'what', 'how', 'why', 'when', 'where', 'hotel', 'room', 'night', 'price', 'view', 'thank', 'hello', 'welcome', 'service', 'staff', 'property'];
            
            foreach ($words as $word) {
                if (in_array($word, $frenchWords)) $frenchCount++;
                if (in_array($word, $englishWords)) $englishCount++;
            }
            
            return $frenchCount > $englishCount ? 'FR' : 'EN';
        }
        
        return 'FR';
    }

    public function translate(string $text, string $targetLang, string $sourceLang = null): string
    {
        $text = trim($text);
        
        if (empty($text)) {
            return $text;
        }
        
        if (!$sourceLang) {
            $sourceLang = $this->detectLanguage($text);
        }
        
        if ($sourceLang === $targetLang) {
            return $text;
        }
        
        $cacheKey = md5($text . $sourceLang . $targetLang);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        $sourceCode = match($sourceLang) {
            'FR' => 'fr',
            'EN' => 'en',
            'AR' => 'ar',
            'DE' => 'de',
            'ES' => 'es',
            'IT' => 'it',
            'PT' => 'pt',
            default => 'fr'
        };
        
        $targetCode = match($targetLang) {
            'FR' => 'fr',
            'EN' => 'en',
            'AR' => 'ar',
            'DE' => 'de',
            'ES' => 'es',
            'IT' => 'it',
            'PT' => 'pt',
            default => 'en'
        };
        
        // Try SimplyTranslate first
        try {
            $response = $this->httpClient->request('POST', 'https://api.simplytranslate.ai/translate', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'text' => $text,
                    'from' => $sourceCode,
                    'to' => $targetCode
                ],
                'timeout' => 10
            ]);
            
            $data = $response->toArray();
            
            if (isset($data['translated_text'])) {
                $translatedText = $data['translated_text'];
                $this->cache[$cacheKey] = $translatedText;
                return $translatedText;
            }
        } catch (\Exception $e) {
        }
        
        // Fallback: Google Translate (unofficial)
        try {
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=' . $sourceCode . '&tl=' . $targetCode . '&dt=t&q=' . urlencode($text);
            $response = $this->httpClient->request('GET', $url, ['timeout' => 10]);
            
            $data = $response->toArray();
            
            if (isset($data[0]) && is_array($data[0]) && isset($data[0][0])) {
                $translatedText = $data[0][0][0];
                if ($translatedText) {
                    $this->cache[$cacheKey] = $translatedText;
                    return $translatedText;
                }
            }
        } catch (\Exception $e) {
        }
        
        return $text;
    }

    public function translateMultiple(array $texts, string $targetLang, string $sourceLang = null): array
    {
        $translations = [];
        foreach ($texts as $key => $text) {
            $translations[$key] = $this->translate($text, $targetLang, $sourceLang);
        }
        return $translations;
    }

    public function getBrowserLanguage(): string
    {
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr-FR';
        $lang = substr($lang, 0, 2);
        
        return match(strtoupper($lang)) {
            'EN' => 'EN',
            'FR' => 'FR',
            'AR' => 'AR',
            'DE' => 'DE',
            'ES' => 'ES',
            'IT' => 'IT',
            'PT' => 'PT',
            default => 'FR',
        };
    }
}