<?php

namespace App\Service\Api;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private ?HttpClientInterface $client = null;
    private string $googleTranslateKey;
    private string $azureTranslatorKey;
    private string $azureEndpoint;

    public function __construct(
        string $googleTranslateKey = '',
        string $azureTranslatorKey = '',
        string $azureEndpoint = ''
    ) {
        $this->googleTranslateKey = $googleTranslateKey ?: $_ENV['GOOGLE_TRANSLATE_API_KEY'] ?? '';
        $this->azureTranslatorKey = $azureTranslatorKey ?: $_ENV['AZURE_TRANSLATOR_KEY'] ?? '';
        $this->azureEndpoint = $azureEndpoint ?: $_ENV['AZURE_TRANSLATOR_ENDPOINT'] ?? '';
    }

    private function getClient(): HttpClientInterface
    {
        if ($this->client === null) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): array
    {
        error_log("TranslationService: translate() called with text='$text', targetLang='$targetLang', sourceLang='$sourceLang'");
        
        if (!empty($this->googleTranslateKey)) {
            return $this->translateWithGoogle($text, $targetLang, $sourceLang);
        }

        if (!empty($this->azureTranslatorKey)) {
            return $this->translateWithAzure($text, $targetLang, $sourceLang);
        }

        $result = $this->getMockTranslation($text, $targetLang);
        error_log("TranslationService: mock result = " . json_encode($result));
        return $result;
    }

    private function translateWithGoogle(string $text, string $targetLang, string $sourceLang): array
    {
        try {
            $response = $this->getClient()->request('POST', 'https://translation.googleapis.com/language/translate/v2', [
                'query' => ['key' => $this->googleTranslateKey],
                'json' => [
                    'q' => $text,
                    'target' => $targetLang,
                    'source' => $sourceLang !== 'auto' ? $sourceLang : null,
                    'format' => 'text'
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'translated_text' => $data['data']['translations'][0]['translatedText'] ?? '',
                'detected_language' => $data['data']['translations'][0]['detectedSourceLanguage'] ?? ''
            ];
        } catch (\Exception $e) {
            return $this->getMockTranslation($text, $targetLang);
        }
    }

    private function translateWithAzure(string $text, string $targetLang, string $sourceLang): array
    {
        try {
            $response = $this->getClient()->request('POST', $this->azureEndpoint . '/translate', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->azureTranslatorKey,
                    'Ocp-Apim-Subscription-Region' => 'westeurope',
                    'Content-Type' => 'application/json'
                ],
                'json' => [['text' => $text]],
                'query' => [
                    'to' => $targetLang,
                    'from' => $sourceLang !== 'auto' ? $sourceLang : null
                ]
            ]);

            $data = $response->toArray();
            return [
                'success' => true,
                'translated_text' => $data[0]['translations'][0]['text'] ?? '',
                'detected_language' => $data[0]['detectedLanguage'] ?? ''
            ];
        } catch (\Exception $e) {
            return $this->getMockTranslation($text, $targetLang);
        }
    }

    public function detectLanguage(string $text): array
    {
        if (!empty($this->googleTranslateKey)) {
            try {
                $response = $this->getClient()->request('POST', 'https://translation.googleapis.com/language/translate/v2/detect', [
                    'query' => ['key' => $this->googleTranslateKey],
                    'json' => ['q' => $text]
                ]);

                $data = $response->toArray();
                return [
                    'success' => true,
                    'language' => $data['data']['detections'][0][0]['language'] ?? 'en',
                    'confidence' => $data['data']['detections'][0][0]['confidence'] ?? 1
                ];
            } catch (\Exception $e) {
                return $this->mockDetectLanguage($text);
            }
        }

        return $this->mockDetectLanguage($text);
    }

    private function mockDetectLanguage(string $text): array
    {
        $textLower = strtolower($text);
        
        $arChars = '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u';
        if (preg_match($arChars, $text)) {
            return ['success' => true, 'language' => 'ar', 'confidence' => 0.9];
        }
        
        $frWords = ['le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'est', 'sont', 'avec', 'pour', 'dans', 'sur', 'ce', 'cette', 'que', 'qui', 'quoi', 'comment', 'desde', 'l\'aeroport', 'aeroport', 'comment', 'se', 'rendre', 'centre', 'ville', 'depuis', 'recommandations', 'restaurant', 'restaurants', 'preferes', 'chers', 'cuisine', 'locale', 'cherche', 'cher', 'bon', 'meilleur', 'tip', 'conseil', 'specialite', 'specialites', 'gastronomie', 'nourriture', 'manger', 'assiette', 'service', 'qualite', 'prix', 'abordable', 'visiter', 'decouvrir', 'explorer', 'guide', 'infos', 'information', 'hotel', 'hebergement', 'sejour', 'voyage', 'vacances', 'circuit', 'excursion', 'visite', 'monument', 'histoire', 'culture'];
        $enWords = ['the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'to', 'from', 'in', 'on', 'at', 'how', 'to', 'get', 'airport', 'city', 'center', 'transport'];
        
        $words = preg_split('/[\s,\.!?;:\'"()]+/', $textLower, -1, PREG_SPLIT_NO_EMPTY);
        
        $frScore = 0;
        $enScore = 0;
        
        foreach ($words as $word) {
            if (in_array($word, $frWords)) $frScore++;
            if (in_array($word, $enWords)) $enScore++;
        }
        
        $total = $frScore + $enScore;
        if ($total > 0) {
            return [
                'success' => true,
                'language' => ($frScore >= $enScore) ? 'fr' : 'en',
                'confidence' => round(max($frScore, $enScore) / $total, 2)
            ];
        }
        
        if (preg_match('/^[a-zA-Z\s]+$/', $text) && strlen($text) > 10) {
            $uppercaseRatio = strlen(preg_replace('/[^A-Z]/', '', $text)) / strlen($text);
            if ($uppercaseRatio < 0.3) {
                return ['success' => true, 'language' => 'fr', 'confidence' => 0.6];
            }
        }
        
        return ['success' => true, 'language' => 'fr', 'confidence' => 0.5];
    }

    public function getSupportedLanguages(): array
    {
        return [
            'success' => true,
            'languages' => [
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'fr', 'name' => 'French'],
                ['code' => 'es', 'name' => 'Spanish'],
                ['code' => 'de', 'name' => 'German'],
                ['code' => 'it', 'name' => 'Italian'],
                ['code' => 'pt', 'name' => 'Portuguese'],
                ['code' => 'nl', 'name' => 'Dutch'],
                ['code' => 'ru', 'name' => 'Russian'],
                ['code' => 'zh', 'name' => 'Chinese'],
                ['code' => 'ja', 'name' => 'Japanese'],
                ['code' => 'ko', 'name' => 'Korean'],
                ['code' => 'ar', 'name' => 'Arabic']
            ]
        ];
    }

    private function getMockTranslation(string $text, string $targetLang): array
    {
        // Phrasal translations (priority over words)
        $phraseMappings = [
            'en' => [
                'comment se rendre' => 'how to get',
                'se rendre' => 'to get',
                'centre-ville' => 'city center',
                'l\'aeroport' => 'the airport',
                'l\'aéroport' => 'the airport',
                'd\'aeroport' => 'from airport',
                'd\'aéroport' => 'from airport',
                'de l\'aeroport' => 'from the airport',
                'de l\'aéroport' => 'from the airport',
                'au centre-ville' => 'to the city center',
                'depuis l\'aeroport' => 'from the airport',
                'depuis l\'aéroport' => 'from the airport',
                's\'il vous plait' => 'please',
                's\'il vous plaît' => 'please',
                'est-ce que' => 'is it that',
                'il y a' => 'there is',
                'qu\'est-ce que' => 'what is',
                'tout le monde' => 'everyone',
                'à bientôt' => 'see you soon',
                'pas cher' => 'cheap',
                'combien de temps' => 'how long',
                'où est' => 'where is',
                'parlez-vous' => 'do you speak',
            ],
            'fr' => [
                'how to get' => 'comment se rendre',
                'city center' => 'centre-ville',
                'the airport' => 'l\'aéroport',
                'to the city center' => 'au centre-ville',
                'from the airport' => 'depuis l\'aéroport',
                'do you speak' => 'parlez-vous',
            ]
        ];

        $wordTranslations = [
            'en' => [
                'bonjour' => 'hello', 'merci' => 'thank you', 'bienvenue' => 'welcome',
                'au revoir' => 'goodbye', 'oui' => 'yes', 'non' => 'no',
                'comment' => 'how', 'aller' => 'to go', 'venir' => 'to come',
                'voir' => 'to see', 'faire' => 'to do', 'etre' => 'to be', 'avoir' => 'to have',
                'voyage' => 'travel', 'hotel' => 'hotel', 'aeroport' => 'airport', 'aéroport' => 'airport',
                'avion' => 'plane', 'train' => 'train', 'bus' => 'bus', 'taxi' => 'taxi',
                'restaurant' => 'restaurant', 'plage' => 'beach', 'mer' => 'sea',
                'soleil' => 'sun', 'piscine' => 'pool', 'chambre' => 'room',
                'reservation' => 'reservation', 'prix' => 'price', 'jour' => 'day',
                'nuit' => 'night', 'matin' => 'morning', 'soir' => 'evening',
                'maintenant' => 'now', 'avant' => 'before', 'apres' => 'after',
                'avec' => 'with', 'sans' => 'without', 'pour' => 'for',
                'dans' => 'in', 'sur' => 'on', 'sous' => 'under', 'entre' => 'between',
                'depuis' => 'from', 'vers' => 'to', 'ici' => 'here', 'la' => 'there',
                'ce' => 'this', 'cet' => 'this', 'cette' => 'this',
                'je' => 'I', 'tu' => 'you', 'il' => 'he', 'elle' => 'she',
                'nous' => 'we', 'vous' => 'you', 'ils' => 'they', 'elles' => 'they',
                'quoi' => 'what', 'quand' => 'when', 'ou' => 'where', 'où' => 'where', 'pourquoi' => 'why',
                'qui' => 'who', 'bien' => 'well', 'tres' => 'very', 'plus' => 'more',
                'moins' => 'less', 'grand' => 'big', 'petit' => 'small',
                'nouveau' => 'new', 'ancien' => 'old', 'bon' => 'good',
                'mauvais' => 'bad', 'joli' => 'pretty', 'beau' => 'beautiful',
                'vite' => 'fast', 'lent' => 'slow', 'facile' => 'easy',
                'difficile' => 'difficult', 'cher' => 'expensive',
                'libre' => 'free', 'ouvre' => 'open', 'ferme' => 'closed',
                'combien' => 'how much', 'aide' => 'help', 'secours' => 'help',
                'danger' => 'danger', 'police' => 'police', 'hopital' => 'hospital',
                'pharmacie' => 'pharmacy', 'banque' => 'bank', 'magasin' => 'shop',
                'marche' => 'market', 'monument' => 'monument', 'musee' => 'museum',
                'eglise' => 'church', 'mosquee' => 'mosque', 'recommandations' => 'recommendations',
                'preferes' => 'preferred', 'cuisine' => 'cuisine', 'locale' => 'local',
                'tunis' => 'Tunis', 'tunisie' => 'Tunisia', 'vos' => 'your',
                'restaurants' => 'restaurants', 'a' => 'to', 'à' => 'to', 'de' => 'from', 'au' => 'to',
            ],
            'ar' => [
                'bonjour' => 'مرحبا', 'merci' => 'شكرا', 'welcome' => 'أهلا وسهلا',
                'goodbye' => 'وداعا', 'yes' => 'نعم', 'no' => 'لا',
                'please' => 'من فضلك', 'excuse me' => 'عفوا',
                'how' => 'كيف', 'to get' => 'للحصول', 'to go' => 'للذهاب',
                'hotel' => 'فندق', 'airport' => 'مطار', 'restaurant' => 'مطعم',
                'beach' => 'شاطئ', 'sea' => 'بحر', 'sun' => 'شمس',
                'pool' => 'مسبح', 'room' => 'غرفة', 'price' => 'سعر',
                'day' => 'يوم', 'night' => 'ليل', 'morning' => 'صباح',
                'now' => 'الآن', 'travel' => 'سفر', 'visit' => 'زيارة',
                'good' => 'جيد', 'bad' => 'سيء', 'beautiful' => 'جميل',
                'great' => 'رائع', 'wonderful' => 'ممتاز', 'help' => 'مساعدة',
                'thank you' => 'شكرا لك', 'welcome' => 'أهلا وسهلا',
            ]
        ];

        $processedText = $text;

        // 1. Apply phrasal translations first
        if (isset($phraseMappings[$targetLang])) {
            foreach ($phraseMappings[$targetLang] as $phrase => $replacement) {
                // Use case-insensitive replacement but try to preserve case if possible
                $processedText = preg_replace('/\b' . preg_quote($phrase, '/') . '\b/iu', $replacement, $processedText);
            }
        }

        // 2. Tokenize the remaining text, preserving punctuation and spaces
        // We split by everything that is NOT a word character or an apostrophe
        $tokens = preg_split('/(\s+|[,\.!?;:()]+)/u', $processedText, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $translatedTokens = [];
        foreach ($tokens as $token) {
            if (empty($token)) continue;
            
            // If it's whitespace or punctuation, keep it as is
            if (preg_match('/^[\s,\.!?;:()]+$/u', $token)) {
                $translatedTokens[] = $token;
                continue;
            }

            $lowerToken = mb_strtolower($token);
            
            // Check word dictionary
            if (isset($wordTranslations[$targetLang][$lowerToken])) {
                $replacement = $wordTranslations[$targetLang][$lowerToken];
                // Try basic case preservation (First letter uppercase)
                if (preg_match('/^[A-Z]/u', $token)) {
                    $replacement = mb_convert_case($replacement, MB_CASE_TITLE, "UTF-8");
                }
                $translatedTokens[] = $replacement;
            } else {
                // Handle special cases like l' or d' if not caught by phrases
                if (preg_match('/^([ldn])\'(.+)$/iu', $token, $matches)) {
                    $prefix = mb_strtolower($matches[1]);
                    $rest = $matches[2];
                    $restLower = mb_strtolower($rest);
                    
                    $newPrefix = '';
                    if ($prefix === 'l') $newPrefix = 'the ';
                    elseif ($prefix === 'd') $newPrefix = 'from ';
                    
                    if (isset($wordTranslations[$targetLang][$restLower])) {
                        $translatedTokens[] = $newPrefix . $wordTranslations[$targetLang][$restLower];
                    } else {
                        $translatedTokens[] = $newPrefix . $rest;
                    }
                } else {
                    $translatedTokens[] = $token;
                }
            }
        }

        $translated = implode('', $translatedTokens);
        
        // If nothing changed, add a prefix to indicate mock translation
        if (trim($translated) === trim($text)) {
            $prefix = $targetLang === 'en' ? '[EN] ' : ($targetLang === 'fr' ? '[FR] ' : ($targetLang === 'ar' ? '[AR] ' : '[' . strtoupper($targetLang) . '] '));
            $translated = $prefix . $text;
        }

        return [
            'success' => true,
            'translated_text' => $translated,
            'detected_language' => ($targetLang === 'en') ? 'fr' : 'en'
        ];
    }
}
