<?php

namespace App\Service;

class LocalEmbeddingService
{
    private array $vocabulary = [];
    private int $dimensions = 384;
    private array $stopWords = [
        'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux',
        'et', 'ou', 'mais', 'donc', 'car', 'ni', 'que', 'qui', 'quoi',
        'ce', 'cette', 'ces', 'il', 'elle', 'ils', 'elles', 'nous', 'vous',
        'je', 'tu', 'je', 'on', 'se', 'son', 'sa', 'ses', 'mon', 'ma', 'mes',
        'votre', 'notre', 'leur', 'leurs', 'à', 'en', 'dans', 'sur', 'pour',
        'avec', 'sans', 'par', 'pas', 'plus', 'moins', 'très', 'bien', 'mal',
        'être', 'avoir', 'faire', 'pouvoir', 'vouloir', 'savoir', 'falloir',
        'est', 'sont', 'était', 'sera', 'été', 'été', 'ai', 'as', 'a', 'avons', 'avez', 'ont',
        'été', 'suis', 'es', 'sommes', 'êtes', 'sont'
    ];

    public function isEnabled(): bool
    {
        return true;
    }

    public function generateEmbedding(string $text): array
    {
        $words = $this->tokenize($text);
        $vector = array_fill(0, $this->dimensions, 0.0);
        
        $wordVectors = $this->getWordVectors();
        $matchedWords = 0;
        
        foreach ($words as $word) {
            if (isset($wordVectors[$word])) {
                $wordVec = $wordVectors[$word];
                for ($i = 0; $i < $this->dimensions; $i++) {
                    $vector[$i] += $wordVec[$i];
                }
                $matchedWords++;
            }
        }
        
        if ($matchedWords > 0) {
            $magnitude = sqrt(array_sum(array_map(fn($x) => $x * $x, $vector)));
            if ($magnitude > 0) {
                for ($i = 0; $i < $this->dimensions; $i++) {
                    $vector[$i] /= $magnitude;
                }
            }
        }
        
        return $vector;
    }

    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $this->stopWords));
    }

    private function getWordVectors(): array
    {
        return [
            'voyage' => $this->hashVector('travel'),
            'voyager' => $this->hashVector('travel'),
            'vacances' => $this->hashVector('vacation'),
            'tunisie' => $this->hashVector('tunisia'),
            'tunis' => $this->hashVector('tunis'),
            'hotel' => $this->hashVector('hotel'),
            'hébergement' => $this->hashVector('accommodation'),
            'circuit' => $this->hashVector('tour'),
            'djerba' => $this->hashVector('djerba'),
            'hammamet' => $this->hashVector('hammamet'),
            'sidi' => $this->hashVector('sidi_bou'),
            'prix' => $this->hashVector('price'),
            'réservation' => $this->hashVector('booking'),
            'réserver' => $this->hashVector('booking'),
            'vol' => $this->hashVector('flight'),
            'avion' => $this->hashVector('flight'),
            'transfert' => $this->hashVector('transfer'),
            'excursion' => $this->hashVector('excursion'),
            'activité' => $this->hashVector('activity'),
            'plage' => $this->hashVector('beach'),
            'restaurant' => $this->hashVector('restaurant'),
            'météo' => $this->hashVector('weather'),
            'climat' => $this->hashVector('climate'),
            'recommander' => $this->hashVector('recommend'),
            'meilleur' => $this->hashVector('best'),
            'bon' => $this->hashVector('good'),
            'beau' => $this->hashVector('beautiful'),
            'visiter' => $this->hashVector('visit'),
            'visite' => $this->hashVector('visit'),
            'découvrir' => $this->hashVector('discover'),
            'explorer' => $this->hashVector('explore'),
            'proche' => $this->hashVector('near'),
            'loin' => $this->hashVector('far'),
            'quartier' => $this->hashVector('area'),
            'centre' => $this->hashVector('center'),
            'distance' => $this->hashVector('distance'),
            'horaire' => $this->hashVector('schedule'),
            'disponible' => $this->hashVector('available'),
            'offre' => $this->hashVector('offer'),
            'promotion' => $this->hashVector('promotion'),
            'réduction' => $this->hashVector('discount'),
            'famille' => $this->hashVector('family'),
            'enfant' => $this->hashVector('child'),
            'couple' => $this->hashVector('couple'),
            'amusement' => $this->hashVector('fun'),
            'nuit' => $this->hashVector('night'),
            'jour' => $this->hashVector('day'),
            'semaine' => $this->hashVector('week'),
            'matin' => $this->hashVector('morning'),
            'soir' => $this->hashVector('evening'),
        ];
    }

    private function hashVector(string $seed): array
    {
        srand(crc32($seed));
        $vector = [];
        for ($i = 0; $i < $this->dimensions; $i++) {
            $vector[] = (rand(-1000, 1000) / 1000.0);
        }
        srand();
        $mag = sqrt(array_sum(array_map(fn($x) => $x * $x, $vector)));
        return array_map(fn($x) => $x / $mag, $vector);
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || empty($a)) return 0;
        
        $dot = 0;
        $magA = 0;
        $magB = 0;
        
        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $magA += $a[$i] * $a[$i];
            $magB += $b[$i] * $b[$i];
        }
        
        $magA = sqrt($magA);
        $magB = sqrt($magB);
        
        if ($magA == 0 || $magB == 0) return 0;
        
        return $dot / ($magA * $magB);
    }
}
